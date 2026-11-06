<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->deleteIfExists('file.upload_background_qr');
        $this->migrator->add('file.upload_background_qr', '');
    }
};

