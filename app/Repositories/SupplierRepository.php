<?php

namespace App\Repositories;

use App\Models\Commune;
use App\Models\District;
use App\Models\Province;
use App\Models\Tenant\Address;
use App\Models\Tenant\Supplier;
use App\Models\Village;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Supplier::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $query->where(function ($query) use ($value) {
                $term = strtolower(trim($value));
                $query->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(phone)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(email)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(contact_person)'), 'LIKE', "%{$term}%");

                if (is_numeric($value)) {
                    $query->orWhere('id', (int) $value);
                }
            });
        });

        $suppliers = QueryBuilder::for(Supplier::class)
            ->defaultSort('-id')
            ->allowedFields([
                'id',
                'name',
                'phone',
                'email',
                'contact_person',
                'address',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'name',
                'phone',
                'email',
                'contact_person',
                'created_at',
            ])
            ->allowedFilters([
                'name',
                'phone',
                'email',
                'contact_person',
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $suppliers;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'name', label: 'Name', sortable: true, searchable: true);
        $table->column(key: 'phone', label: 'Phone', sortable: true, searchable: true);
        $table->column(key: 'email', label: 'Email', sortable: true, searchable: true);
        $table->column(key: 'contact_person', label: 'Contact Person', sortable: true, searchable: true);
        $table->column(key: 'created_at', label: 'Created Date', sortable: true);
    }

    public function findWithAddress($id)
    {
        $supplier = $this->find($id);
        if ($supplier) {
            $supplier->load([
                'addressRecord.province',
                'addressRecord.district',
                'addressRecord.commune',
                'addressRecord.village',
            ]);
        }
        return $supplier;
    }

    public function getProvinces()
    {
        $provinces = Province::orderBy('name_en')->get(['id', 'name_en', 'name_kh', 'code']);
        if ($provinces->isEmpty()) {
            $provinces = tenancy()->central(function () {
                return Province::orderBy('name_en')->get(['id', 'name_en', 'name_kh', 'code']);
            });
        }
        return $provinces;
    }

    public function getDistricts($provinceId)
    {
        $districts = District::where('province_id', $provinceId)->orderBy('name_en')->get(['id', 'province_id', 'name_en', 'name_kh', 'code']);
        if ($districts->isEmpty()) {
            $districts = tenancy()->central(function () use ($provinceId) {
                return District::where('province_id', $provinceId)->orderBy('name_en')->get(['id', 'province_id', 'name_en', 'name_kh', 'code']);
            });
        }
        return $districts;
    }

    public function getCommunes($districtId)
    {
        $communes = Commune::where('district_id', $districtId)->orderBy('name_en')->get(['id', 'district_id', 'name_en', 'name_kh', 'code']);
        if ($communes->isEmpty()) {
            $communes = tenancy()->central(function () use ($districtId) {
                return Commune::where('district_id', $districtId)->orderBy('name_en')->get(['id', 'district_id', 'name_en', 'name_kh', 'code']);
            });
        }
        return $communes;
    }

    public function getVillages($communeId)
    {
        $villages = Village::where('commune_id', $communeId)->orderBy('name_en')->get(['id', 'commune_id', 'name_en', 'name_kh', 'code']);
        if ($villages->isEmpty()) {
            $villages = tenancy()->central(function () use ($communeId) {
                return Village::where('commune_id', $communeId)->orderBy('name_en')->get(['id', 'commune_id', 'name_en', 'name_kh', 'code']);
            });
        }
        return $villages;
    }

    public function saveAddress($supplier, array $addressData)
    {
        $provinceId = $addressData['province_id'] ?? null;
        $districtId = $addressData['district_id'] ?? null;
        $communeId = $addressData['commune_id'] ?? null;
        $villageId = $addressData['village_id'] ?? null;

        if ($provinceId) {
            Address::updateOrCreate(
                [
                    'addressable_id' => $supplier->id,
                    'addressable_type' => Supplier::class,
                ],
                [
                    'province_id' => $provinceId,
                    'district_id' => $districtId,
                    'commune_id' => $communeId,
                    'village_id' => $villageId
                ]
            );

            // Construct human-readable summary string for the supplier's address column
            $parts = [];
            if ($villageId) {
                $v = Village::find($villageId) ?? tenancy()->central(fn () => Village::find($villageId));
                if ($v) $parts[] = $v->name_en;
            }
            if ($communeId) {
                $c = Commune::find($communeId) ?? tenancy()->central(fn () => Commune::find($communeId));
                if ($c) $parts[] = $c->name_en;
            }
            if ($districtId) {
                $d = District::find($districtId) ?? tenancy()->central(fn () => District::find($districtId));
                if ($d) $parts[] = $d->name_en;
            }
            if ($provinceId) {
                $p = Province::find($provinceId) ?? tenancy()->central(fn () => Province::find($provinceId));
                if ($p) $parts[] = $p->name_en;
            }

            if (!empty($parts)) {
                $supplier->update(['address' => implode(', ', $parts)]);
            }
        } elseif (isset($addressData['address'])) {
            $supplier->update(['address' => $addressData['address']]);
        }
    }

    public function delete($id)
    {
        $supplier = is_object($id) ? $id : $this->find($id);
        if ($supplier) {
            Address::where('addressable_id', $supplier->id)
                ->where('addressable_type', Supplier::class)
                ->delete();

            return parent::delete($supplier);
        }
        return false;
    }
}
