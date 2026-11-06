<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;


return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->deleteIfExists('file.upload_background_qr_school');
        $this->migrator->add('file.upload_background_qr_school', '');
        $this->migrator->deleteIfExists('format_qr.format_background_qr_school');
        $this->migrator->add('format_qr.format_background_qr_school', []);
    }
};
