<?php

namespace App\Repositories;

use App\Models\Tenant\Category;
use App\Models\Tenant\Hospital;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Category::class)
             ->setRepositoryId('category.repository');
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

    public function getDataTable(string $type)
    {
        $hospitalId = $this->getHospitalId();

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim($value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(description)'), 'LIKE', "%{$term}%");

                if (is_numeric($value)) {
                    $query->orWhere('id', (int) $value)
                          ->orWhere('code', (int) $value);
                }
            });
        });

        $baseQuery = Category::query()
            ->where('type', $type)
            ->where('hospital_id', $hospitalId);

        $categories = QueryBuilder::for($baseQuery)
            ->defaultSort('-id')
            ->allowedFields([
                'id',
                'hospital_id',
                'name',
                'description',
                'type',
                'is_active',
                'code',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ])
            ->allowedSorts([
                'id',
                'name',
                'description',
                'is_active',
                'code',
                'created_at',
            ])
            ->allowedFilters([
                'name',
                AllowedFilter::exact('is_active'),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $categories;
    }

    public function columnsDataTable(InertiaTable $table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->selectFilter(key: 'is_active', label: 'Status', options: [
            '1' => 'Active',
            '0' => 'Inactive',
        ]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'name', label: 'Name', sortable: true, searchable: true);
        $table->column(key: 'description', label: 'Description', sortable: true, searchable: true);
        $table->column(key: 'is_active', label: 'Active', sortable: true);
        $table->column(key: 'created_at', label: 'Created Date', sortable: true);
    }

    public function findForType(int $id, string $type): ?Category
    {
        return Category::where('type', $type)
            ->where('hospital_id', $this->getHospitalId())
            ->where('id', $id)
            ->first();
    }

    public function createForType(string $type, array $data): Category
    {
        $data['type'] = $type;
        $data['hospital_id'] = $this->getHospitalId();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // Ensure boolean cast for is_active
        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        } else {
            $data['is_active'] = true;
        }

        return Category::create($data);
    }

    public function updateForType(int $id, string $type, array $data): ?Category
    {
        $category = $this->findForType($id, $type);
        if (!$category) {
            return null;
        }

        // Prevent tampering with type and hospital_id
        unset($data['type'], $data['hospital_id']);
        $data['updated_by'] = auth()->id();

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $category->update($data);
        return $category;
    }

    public function deleteForType(int $id, string $type): bool
    {
        $category = $this->findForType($id, $type);
        if (!$category) {
            return false;
        }

        return (bool) $category->delete();
    }
}
