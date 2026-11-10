<?php

namespace App\Repositories;

use App\Models\Tenant\Category;
use App\Models\Tenant\Hospital;
use App\Models\Tenant\Service;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Service::class)
             ->setRepositoryId('service.repository');
    }

    public function getHospitalId(): int
    {
        $hospital = Hospital::first();
        if ($hospital) {
            return $hospital->id;
        }

        $userId = auth()->id() ?? 1;
        $created = Hospital::create([
            'name' => 'Default Hospital',
            'user_id' => $userId,
        ]);

        return $created->id;
    }

    public function getDataTable()
    {
        $hospitalId = $this->getHospitalId();

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(services.name)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(services.description)'), 'LIKE', "%{$term}%")
                    ->orWhereHas('category', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%");
                    });

                if (is_numeric($value)) {
                    $query->orWhere('services.code', (int) $value)
                          ->orWhere('services.price', (float) $value)
                          ->orWhere('services.cost', (float) $value);
                }
            });
        });

        $baseQuery = Service::query()
            ->with('category')
            ->where('services.hospital_id', $hospitalId);

        $services = QueryBuilder::for($baseQuery)
            ->defaultSort('-id')
            ->allowedFields([
                'id',
                'hospital_id',
                'name',
                'description',
                'price',
                'discount',
                'type',
                'cost',
                'qty',
                'supplier_id',
                'commission',
                'commission_type',
                'status',
                'code',
                'category_id',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'code',
                'name',
                'price',
                'discount',
                'cost',
                'status',
                'created_at',
            ])
            ->allowedFilters([
                'name',
                'code',
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('status'),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $services;
    }

    public function columnsDataTable(InertiaTable $table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->selectFilter(key: 'status', label: 'Status', options: [
            '1' => 'Enable',
            '0' => 'Disable',
        ]);
        $table->withGlobalSearch();

        // Exact column order: No., Name, Category, Price, Discount, Cost, Actions
        $table->column(key: 'actions', label: 'Actions');
        $table->column(key: 'code', label: 'No.', sortable: true);
        $table->column(key: 'name', label: 'Name', sortable: true, searchable: true);
        $table->column(key: 'category_name', label: 'Category');
        $table->column(key: 'price', label: 'Price', sortable: true);
        $table->column(key: 'discount', label: 'Discount', sortable: true);
        $table->column(key: 'cost', label: 'Cost', sortable: true);
    }

    public function getServiceCategories(): Collection
    {
        return Category::where('type', 'service')
            ->where('hospital_id', $this->getHospitalId())
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function findWithDetails(int $id): ?Service
    {
        return Service::with('category')
            ->where('hospital_id', $this->getHospitalId())
            ->where('id', $id)
            ->first();
    }

    public function createService(array $data): Service
    {
        $data['hospital_id'] = $this->getHospitalId();

        // Ensure description is string to satisfy non-null DB column
        if (!isset($data['description']) || $data['description'] === null) {
            $data['description'] = '';
        }

        // Validate that category_id (if provided) is a service category belonging to this hospital
        if (!empty($data['category_id'])) {
            $cat = Category::where('id', $data['category_id'])
                ->where('hospital_id', $data['hospital_id'])
                ->where('type', 'service')
                ->first();
            if (!$cat) {
                $data['category_id'] = null;
            }
        } else {
            $data['category_id'] = null;
        }

        // Numeric sanitization
        $data['price'] = (float) ($data['price'] ?? 0);
        $data['cost'] = (float) ($data['cost'] ?? 0);
        $data['discount'] = isset($data['discount']) && $data['discount'] !== '' ? (float) $data['discount'] : 0;
        $data['commission'] = isset($data['commission']) && $data['commission'] !== '' ? (float) $data['commission'] : 0;
        $data['commission_type'] = !empty($data['commission_type']) ? $data['commission_type'] : '%';
        $data['status'] = isset($data['status']) ? (bool) $data['status'] : true;

        return Service::create($data);
    }

    public function updateService(int $id, array $data): ?Service
    {
        $service = $this->findWithDetails($id);
        if (!$service) {
            return null;
        }

        unset($data['hospital_id']);

        if (!isset($data['description']) || $data['description'] === null) {
            $data['description'] = '';
        }

        if (!empty($data['category_id'])) {
            $cat = Category::where('id', $data['category_id'])
                ->where('hospital_id', $this->getHospitalId())
                ->where('type', 'service')
                ->first();
            if (!$cat) {
                $data['category_id'] = null;
            }
        } else {
            $data['category_id'] = null;
        }

        if (array_key_exists('price', $data)) {
            $data['price'] = (float) $data['price'];
        }
        if (array_key_exists('cost', $data)) {
            $data['cost'] = (float) $data['cost'];
        }
        if (array_key_exists('discount', $data)) {
            $data['discount'] = $data['discount'] !== '' && $data['discount'] !== null ? (float) $data['discount'] : 0;
        }
        if (array_key_exists('commission', $data)) {
            $data['commission'] = $data['commission'] !== '' && $data['commission'] !== null ? (float) $data['commission'] : 0;
        }
        if (array_key_exists('commission_type', $data)) {
            $data['commission_type'] = !empty($data['commission_type']) ? $data['commission_type'] : '%';
        }
        if (array_key_exists('status', $data)) {
            $data['status'] = (bool) $data['status'];
        }

        $service->update($data);
        return $service;
    }

    public function deleteService(int $id): bool
    {
        $service = $this->findWithDetails($id);
        if (!$service) {
            return false;
        }

        return (bool) $service->delete();
    }
}
