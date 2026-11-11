<?php

namespace App\Repositories;

use App\Models\Tenant\Prefix;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InvoicePrefixRepository extends BaseRepository implements InvoicePrefixRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Prefix::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(prefix_name)'), 'LIKE', "%{$term}%")
                      ->orWhere(DB::raw('LOWER(prefix_format)'), 'LIKE', "%{$term}%");

                if (is_numeric($value)) {
                    $query->orWhere('id', (int) $value);
                }
            });
        });

        return QueryBuilder::for(Prefix::class)
            ->defaultSort('-id')
            ->allowedFields([
                'id',
                'prefix_name',
                'prefix_format',
                'default_prefix',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'prefix_name',
                'prefix_format',
                'default_prefix',
                'created_at',
            ])
            ->allowedFilters([
                'prefix_name',
                'prefix_format',
                AllowedFilter::exact('default_prefix'),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'prefix_name', label: 'Name', sortable: true, searchable: true);
        $table->column(key: 'prefix_format', label: 'Format', sortable: true, searchable: true);
        $table->column(key: 'default_prefix', label: 'Default Prefix', sortable: true);
        $table->column(key: 'created_at', label: 'Created At', sortable: true);
    }

    public function getDefault()
    {
        $default = Prefix::where('default_prefix', true)->first();
        if (!$default) {
            // Check if any prefix exists
            $default = Prefix::first();
            if (!$default) {
                $default = Prefix::create([
                    'prefix_name' => 'INV',
                    'prefix_format' => 'Year-Series',
                    'default_prefix' => true,
                ]);
            } else {
                $default->update(['default_prefix' => true]);
            }
        }
        return $default;
    }

    public function create(array $attributes = [])
    {
        return DB::transaction(function () use ($attributes) {
            $isDefault = !empty($attributes['default_prefix']);

            // If this is set to default, or if there is no other default prefix, make it default
            $existingCount = Prefix::count();
            if ($existingCount === 0 || $isDefault) {
                Prefix::where('default_prefix', true)->update(['default_prefix' => false]);
                $attributes['default_prefix'] = true;
            }

            return Prefix::create($attributes);
        });
    }

    public function update($id, array $attributes = [])
    {
        return DB::transaction(function () use ($id, $attributes) {
            $prefix = Prefix::findOrFail($id);
            $isDefault = !empty($attributes['default_prefix']);

            if ($isDefault) {
                Prefix::where('id', '!=', $id)->where('default_prefix', true)->update(['default_prefix' => false]);
                $attributes['default_prefix'] = true;
            }

            $prefix->update($attributes);
            return $prefix;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $prefix = Prefix::findOrFail($id);
            $wasDefault = $prefix->default_prefix;
            $deleted = $prefix->delete();

            if ($wasDefault) {
                // Set another prefix as default if available
                $newDefault = Prefix::first();
                if ($newDefault) {
                    $newDefault->update(['default_prefix' => true]);
                }
            }

            return $deleted;
        });
    }
}
