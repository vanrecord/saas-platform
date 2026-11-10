<?php

namespace App\Repositories;

use App\Models\Tenant\Category;
use App\Models\Tenant\Hospital;
use App\Models\Tenant\Product;
use App\Models\Tenant\Supplier;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Product::class)
             ->setRepositoryId('product.repository');
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
                $query->where(DB::raw('LOWER(products.name)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(products.description)'), 'LIKE', "%{$term}%")
                    ->orWhereHas('category', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%");
                    })
                    ->orWhereHas('supplier', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%");
                    });

                if (is_numeric($value)) {
                    $query->orWhere('products.code', (int) $value)
                          ->orWhere('products.price', (float) $value)
                          ->orWhere('products.cost', (float) $value)
                          ->orWhere('products.qty', (int) $value);
                }
            });
        });

        $baseQuery = Product::query()
            ->with(['category', 'supplier'])
            ->where('products.hospital_id', $hospitalId);

        $products = QueryBuilder::for($baseQuery)
            ->defaultSort('-id')
            ->allowedFields([
                'id',
                'hospital_id',
                'supplier_id',
                'category_id',
                'code',
                'name',
                'price',
                'cost',
                'qty',
                'status',
                'description',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'code',
                'name',
                'price',
                'cost',
                'qty',
                'status',
                'created_at',
            ])
            ->allowedFilters([
                'name',
                'code',
                AllowedFilter::exact('supplier_id'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('status'),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $products;
    }

    public function columnsDataTable(InertiaTable $table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->selectFilter(key: 'status', label: 'Status', options: [
            '1' => 'Enable',
            '0' => 'Disable',
        ]);
        $table->withGlobalSearch();

        // Exact column order: No., Product, Supplier, Category, Price, Cost, Quantity, Status, Actions
        $table->column(key: 'code', label: 'No.', sortable: true);
        $table->column(key: 'name', label: 'Product', sortable: true, searchable: true);
        $table->column(key: 'supplier_name', label: 'Supplier');
        $table->column(key: 'category_name', label: 'Category');
        $table->column(key: 'price', label: 'Price', sortable: true);
        $table->column(key: 'cost', label: 'Cost', sortable: true);
        $table->column(key: 'qty', label: 'Quantity', sortable: true);
        $table->column(key: 'status', label: 'Status', sortable: true);
        $table->column(key: 'actions', label: 'Actions');
    }

    public function getSuppliers(): Collection
    {
        return Supplier::orderBy('name')->get(['id', 'name']);
    }

    public function getProductCategories(): Collection
    {
        return Category::where('type', 'product')
            ->where('hospital_id', $this->getHospitalId())
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function findWithDetails(int $id): ?Product
    {
        return Product::with(['category', 'supplier'])
            ->where('hospital_id', $this->getHospitalId())
            ->where('id', $id)
            ->first();
    }

    public function createProduct(array $data): Product
    {
        $data['hospital_id'] = $this->getHospitalId();

        if (!isset($data['description']) || $data['description'] === null) {
            $data['description'] = '';
        }

        if (!empty($data['category_id'])) {
            $cat = Category::where('id', $data['category_id'])
                ->where('hospital_id', $data['hospital_id'])
                ->where('type', 'product')
                ->first();
            if (!$cat) {
                $data['category_id'] = null;
            }
        } else {
            $data['category_id'] = null;
        }

        if (!empty($data['supplier_id'])) {
            $supplier = Supplier::find($data['supplier_id']);
            if (!$supplier) {
                $data['supplier_id'] = null;
            }
        } else {
            $data['supplier_id'] = null;
        }

        $data['price'] = (float) ($data['price'] ?? 0);
        $data['cost'] = (float) ($data['cost'] ?? 0);
        $data['qty'] = (int) ($data['qty'] ?? 0);
        $data['status'] = isset($data['status']) ? (bool) $data['status'] : true;

        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): ?Product
    {
        $product = $this->findWithDetails($id);
        if (!$product) {
            return null;
        }

        unset($data['hospital_id']);

        if (array_key_exists('description', $data) && $data['description'] === null) {
            $data['description'] = '';
        }

        if (!empty($data['category_id'])) {
            $cat = Category::where('id', $data['category_id'])
                ->where('hospital_id', $this->getHospitalId())
                ->where('type', 'product')
                ->first();
            if (!$cat) {
                $data['category_id'] = null;
            }
        } else {
            $data['category_id'] = null;
        }

        if (!empty($data['supplier_id'])) {
            $supplier = Supplier::find($data['supplier_id']);
            if (!$supplier) {
                $data['supplier_id'] = null;
            }
        } else {
            $data['supplier_id'] = null;
        }

        if (array_key_exists('price', $data)) {
            $data['price'] = (float) $data['price'];
        }
        if (array_key_exists('cost', $data)) {
            $data['cost'] = (float) $data['cost'];
        }
        if (array_key_exists('qty', $data)) {
            $data['qty'] = (int) $data['qty'];
        }
        if (array_key_exists('status', $data)) {
            $data['status'] = (bool) $data['status'];
        }

        $product->update($data);
        return $product;
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->findWithDetails($id);
        if (!$product) {
            return false;
        }

        return (bool) $product->delete();
    }
}
