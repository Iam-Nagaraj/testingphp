<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // \App\Models\User::factory(10)->create();
        $this->call(RoleSeeder::class);
        $this->call(BusinessClassificationsSeeder::class);

        DB::table('users')->truncate();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@scash.com',
            'password' => Hash::make('12345678'),
            'role_id' => getConfigConstant('ADMIN_ROLE_ID'),
            // 'country_code' => 0
        ]);
        User::factory()->create([
          'name' => 'Merchant',
          'email' => 'merchant@scash.com',
          'password' => Hash::make('12345678'),
          'role_id' => getConfigConstant('MERCHANT_ROLE_ID'),
          // 'country_code' => 0
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

    }
}
