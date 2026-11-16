<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    /**
     * Cache table columns per connection and table name.
     *
     * @var array<string, array<int, string>>
     */
    protected static array $tableColumnsCache = [];

    /**
     * Virtual attributes stored in memory when physical database columns do not exist.
     *
     * @var array<string, mixed>
     */
    protected array $virtualAttributes = [];

    /**
     * Boot the model and register lifecycle hooks.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $media) {
            $media->prepareAttributesForStorage();
        });
    }

    /**
     * Get the list of actual columns present in the database table for this model's connection.
     *
     * @return array<int, string>
     */
    public function getTableColumns(): array
    {
        $connectionName = $this->getConnectionName() ?: config('database.default');
        $table = $this->getTable();
        $cacheKey = "{$connectionName}.{$table}";

        if (! isset(static::$tableColumnsCache[$cacheKey])) {
            try {
                $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($table);
                if (! empty($columns)) {
                    static::$tableColumnsCache[$cacheKey] = $columns;
                } else {
                    static::$tableColumnsCache[$cacheKey] = $this->defaultTenantColumns();
                }
            } catch (\Throwable $e) {
                static::$tableColumnsCache[$cacheKey] = $this->defaultTenantColumns();
            }
        }

        return static::$tableColumnsCache[$cacheKey];
    }

    /**
     * Check if a column exists in the physical table for the active connection.
     */
    public function hasTableColumn(string $column): bool
    {
        return in_array($column, $this->getTableColumns(), true);
    }

    /**
     * Default columns matching the existing tenant media table.
     *
     * @return array<int, string>
     */
    protected function defaultTenantColumns(): array
    {
        return [
            'id',
            'model_id',
            'model_type',
            'collection_name',
            'name',
            'file_name',
            'disk',
            'size',
            'manipulations',
            'custom_properties',
            'order_column',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Accessor for conversions_disk.
     * Falls back to disk if conversions_disk column does not exist.
     */
    public function getConversionsDiskAttribute(): ?string
    {
        if (array_key_exists('conversions_disk', $this->attributes) && $this->attributes['conversions_disk'] !== null) {
            return $this->attributes['conversions_disk'];
        }

        if (isset($this->virtualAttributes['conversions_disk'])) {
            return $this->virtualAttributes['conversions_disk'];
        }

        $custom = $this->getCustomProperty('conversions_disk');
        if ($custom !== null) {
            return $custom;
        }

        return $this->disk ?: config('media-library.disk_name');
    }

    /**
     * Mutator for conversions_disk.
     */
    public function setConversionsDiskAttribute(?string $value): void
    {
        if ($this->hasTableColumn('conversions_disk')) {
            $this->attributes['conversions_disk'] = $value;
        } else {
            $this->virtualAttributes['conversions_disk'] = $value;
            if ($value && $value !== $this->disk) {
                $this->setCustomProperty('conversions_disk', $value);
            }
        }
    }

    /**
     * Accessor for uuid.
     */
    public function getUuidAttribute(): ?string
    {
        if (array_key_exists('uuid', $this->attributes) && $this->attributes['uuid'] !== null) {
            return $this->attributes['uuid'];
        }

        if (isset($this->virtualAttributes['uuid'])) {
            return $this->virtualAttributes['uuid'];
        }

        $custom = $this->getCustomProperty('uuid');
        if ($custom !== null) {
            return $custom;
        }

        return (string) Str::uuid();
    }

    /**
     * Mutator for uuid.
     */
    public function setUuidAttribute(?string $value): void
    {
        if ($this->hasTableColumn('uuid')) {
            $this->attributes['uuid'] = $value;
        } else {
            $this->virtualAttributes['uuid'] = $value;
            if ($value !== null) {
                $this->setCustomProperty('uuid', $value);
            }
        }
    }

    /**
     * Accessor for mime_type.
     */
    public function getMimeTypeAttribute(): ?string
    {
        if (array_key_exists('mime_type', $this->attributes) && $this->attributes['mime_type'] !== null) {
            return $this->attributes['mime_type'];
        }

        if (isset($this->virtualAttributes['mime_type'])) {
            return $this->virtualAttributes['mime_type'];
        }

        $custom = $this->getCustomProperty('mime_type');
        if ($custom !== null) {
            return $custom;
        }

        if (! empty($this->file_name)) {
            return \Spatie\MediaLibrary\Support\File::getMimeType($this->file_name);
        }

        return null;
    }

    /**
     * Mutator for mime_type.
     */
    public function setMimeTypeAttribute(?string $value): void
    {
        if ($this->hasTableColumn('mime_type')) {
            $this->attributes['mime_type'] = $value;
        } else {
            $this->virtualAttributes['mime_type'] = $value;
            if ($value !== null) {
                $this->setCustomProperty('mime_type', $value);
            }
        }
    }

    /**
     * Accessor for generated_conversions.
     */
    public function getGeneratedConversionsAttribute(): array
    {
        if (array_key_exists('generated_conversions', $this->attributes) && $this->attributes['generated_conversions'] !== null) {
            $val = $this->attributes['generated_conversions'];
            return is_string($val) ? (json_decode($val, true) ?: []) : (array) $val;
        }

        if (isset($this->virtualAttributes['generated_conversions'])) {
            return (array) $this->virtualAttributes['generated_conversions'];
        }

        return (array) ($this->getCustomProperty('generated_conversions') ?: []);
    }

    /**
     * Mutator for generated_conversions.
     */
    public function setGeneratedConversionsAttribute($value): void
    {
        if ($this->hasTableColumn('generated_conversions')) {
            $this->attributes['generated_conversions'] = is_array($value) ? json_encode($value) : $value;
        } else {
            $this->virtualAttributes['generated_conversions'] = (array) $value;
            $this->setCustomProperty('generated_conversions', (array) $value);
        }
    }

    /**
     * Accessor for responsive_images.
     */
    public function getResponsiveImagesAttribute(): array
    {
        if (array_key_exists('responsive_images', $this->attributes) && $this->attributes['responsive_images'] !== null) {
            $val = $this->attributes['responsive_images'];
            return is_string($val) ? (json_decode($val, true) ?: []) : (array) $val;
        }

        if (isset($this->virtualAttributes['responsive_images'])) {
            return (array) $this->virtualAttributes['responsive_images'];
        }

        return (array) ($this->getCustomProperty('responsive_images') ?: []);
    }

    /**
     * Mutator for responsive_images.
     */
    public function setResponsiveImagesAttribute($value): void
    {
        if ($this->hasTableColumn('responsive_images')) {
            $this->attributes['responsive_images'] = is_array($value) ? json_encode($value) : $value;
        } else {
            $this->virtualAttributes['responsive_images'] = (array) $value;
            $this->setCustomProperty('responsive_images', (array) $value);
        }
    }

    /**
     * Find media item by UUID even if uuid column does not exist physically.
     */
    public static function findByUuid(string $uuid): ?Model
    {
        $instance = new static;
        if ($instance->hasTableColumn('uuid')) {
            return static::where('uuid', $uuid)->first();
        }

        return static::where('custom_properties', 'like', '%"uuid":"' . $uuid . '"%')->first();
    }

    /**
     * Prepare model attributes for storage. Strips non-existent table columns.
     */
    public function prepareAttributesForStorage(): void
    {
        $tableColumns = $this->getTableColumns();

        // 1. Sync virtual attributes into custom_properties if missing from physical table
        if (! in_array('uuid', $tableColumns, true)) {
            $uuid = $this->virtualAttributes['uuid'] ?? $this->getCustomProperty('uuid');
            if (empty($uuid)) {
                $uuid = (string) Str::uuid();
                $this->virtualAttributes['uuid'] = $uuid;
            }
            if (! $this->hasCustomProperty('uuid')) {
                $this->setCustomProperty('uuid', $uuid);
            }
        }

        if (! in_array('mime_type', $tableColumns, true)) {
            $mime = $this->virtualAttributes['mime_type'] ?? null;
            if ($mime && ! $this->hasCustomProperty('mime_type')) {
                $this->setCustomProperty('mime_type', $mime);
            }
        }

        // 2. Ensure manipulations is non-null text/json
        if (! isset($this->attributes['manipulations'])) {
            $this->attributes['manipulations'] = '[]';
        }

        // 3. Ensure custom_properties is non-null text/json
        if (! isset($this->attributes['custom_properties'])) {
            $this->attributes['custom_properties'] = '[]';
        }

        // 4. Strip any attribute that does not exist in the physical table
        foreach (array_keys($this->attributes) as $attribute) {
            if (! in_array($attribute, $tableColumns, true)) {
                $this->virtualAttributes[$attribute] = $this->attributes[$attribute];
                unset($this->attributes[$attribute]);
            }
        }
    }

    /**
     * Get attributes for insert query.
     */
    protected function getAttributesForInsert(): array
    {
        $this->prepareAttributesForStorage();

        $tableColumns = $this->getTableColumns();

        return array_intersect_key($this->getAttributes(), array_flip($tableColumns));
    }

    /**
     * Get dirty attributes for update query.
     */
    protected function getDirtyForUpdate(): array
    {
        $this->prepareAttributesForStorage();

        $tableColumns = $this->getTableColumns();

        return array_intersect_key(parent::getDirtyForUpdate(), array_flip($tableColumns));
    }
}
