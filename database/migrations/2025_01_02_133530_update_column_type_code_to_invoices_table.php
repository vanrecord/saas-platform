<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        \DB::statement('ALTER TABLE invoices ALTER COLUMN code TYPE INTEGER USING code::INTEGER');
    }

    public function down()
    {
        \DB::statement('ALTER TABLE invoices ALTER COLUMN code TYPE VARCHAR USING code::TEXT');
    }
};
