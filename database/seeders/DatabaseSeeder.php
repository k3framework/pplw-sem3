<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Role;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(base_path('data/demo.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($data['roles'] as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
        foreach ($data['menu_categories'] as $name) {
            MenuCategory::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
        foreach ($data['menu_items'] as $item) {
            $item['menu_category_id'] = MenuCategory::where('name', $item['category'])->value('id');
            unset($item['category']);
            MenuItem::firstOrCreate(['name' => $item['name']], $item);
        }
        foreach ($data['restaurant_tables'] as $table) {
            RestaurantTable::firstOrCreate(['code' => $table['code']], $table);
        }
        foreach ($data['time_slots'] as $slot) {
            TimeSlot::firstOrCreate(['start_time' => $slot['start_time'], 'end_time' => $slot['end_time']], $slot);
        }
        foreach ([
            ['name' => 'Admin Demo', 'email' => 'admin@example.test', 'role' => 'admin'],
            ['name' => 'Pelanggan Demo', 'email' => 'pelanggan@example.test', 'role' => 'customer'],
        ] as $account) {
            User::firstOrCreate(['email' => $account['email']], [
                'name' => $account['name'], 'phone' => '081200000000',
                'password' => 'Distretto123!',
                'role_id' => Role::where('name', $account['role'])->value('id'),
            ]);
        }
    }
}
