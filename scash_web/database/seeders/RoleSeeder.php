<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$maps = array(
			array(
				'id' => 1,
				'name' => 'Super Admin',
			),
			array(
				'id' => 2,
				'name' => 'Admin',

			),
			array(
				'id' => 3,
				'name' => 'Merchant',

			),
			array(
				'id' => 4,
				'name' => 'Customer',

			),
			array(
				'id' => 5,
				'name' => 'Store',

			)
		);

		$newArray = [];
		foreach ($maps as $key => $value) {
			if (!DB::table('roles')->where('id', $value['id'])->exists()) {
				$newArray[] = $value;
			}
		}

		DB::table('roles')->insert($newArray);
	}
}
