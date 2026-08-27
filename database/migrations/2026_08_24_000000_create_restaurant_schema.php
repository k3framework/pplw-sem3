<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('schema.sql')));
    }

    public function down(): void
    {
        foreach (['payments', 'reservations', 'time_slots', 'restaurant_tables', 'menu_items', 'menu_categories', 'users', 'roles'] as $table) {
            DB::statement('drop table if exists '.$table);
        }
    }
};
