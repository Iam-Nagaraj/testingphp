<?php

namespace Database\Seeders;

use App\Models\BusinessCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessClassificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["id"=>"9ed3f669-7d6f-11e3-b545-5404a6144203","name"=>"Food retail and service"],
            ["id"=>"9ed41d89-7d6f-11e3-beff-5404a6144203","name"=>"Manufacturing"],
            ["id"=>"9ed35a3b-7d6f-11e3-83c8-5404a6144203","name"=>"Business to business"],
            ["id"=>"9ed4449e-7d6f-11e3-a32d-5404a6144203","name"=>"Services - other"],
            ["id"=>"9ed3a866-7d6f-11e3-a0ce-5404a6144203","name"=>"Entertainment and media"],
            ["id"=>"9ed41d75-7d6f-11e3-b151-5404a6144203","name"=>"Home and garden"],
            ["id"=>"9ed35a29-7d6f-11e3-930b-5404a6144203","name"=>"Baby"],
            ["id"=>"9ed492c6-7d6f-11e3-80f4-5404a6144203","name"=>"Travel"],
            ["id"=>"9ed38152-7d6f-11e3-9042-5404a6144203","name"=>"Clothing, accessories, and shoes"],
            ["id"=>"9ed3f686-7d6f-11e3-af6e-5404a6144203","name"=>"Health and personal care"],
            ["id"=>"9ed35a2e-7d6f-11e3-a5cf-5404a6144203","name"=>"Beauty and fragrances"],
            ["id"=>"9ed3a846-7d6f-11e3-8a79-5404a6144203","name"=>"Computers, accessories, and services"],
            ["id"=>"9ed44496-7d6f-11e3-865d-5404a6144203","name"=>"Retail"],
            ["id"=>"9ed492bc-7d6f-11e3-9a1b-5404a6144203","name"=>"Toys and hobbies"],
            ["id"=>"9ed4448d-7d6f-11e3-aab2-5404a6144203","name"=>"Pets and animals"],
            ["id"=>"9ed3a854-7d6f-11e3-a193-5404a6144203","name"=>"Education"],
            ["id"=>"9ed248ae-7d6f-11e3-ba6e-5404a6144203","name"=>"Arts, crafts, and collectibles"],
            ["id"=>"9ed3cf5f-7d6f-11e3-8af8-5404a6144203","name"=>"Financial services and products"],
            ["id"=>"9ed3f67d-7d6f-11e3-bf40-5404a6144203","name"=>"Government"],
            ["id"=>"9ed4b9bc-7d6f-11e3-9133-5404a6144203","name"=>"Vehicle sales"],
            ["id"=>"9ed44486-7d6f-11e3-89f8-5404a6144203","name"=>"Nonprofit"],
            ["id"=>"9ed4b9c6-7d6f-11e3-a156-5404a6144203","name"=>"Vehicle service and accessories"],
            ["id"=>"9ed3a85b-7d6f-11e3-8995-5404a6144203","name"=>"Electronics and telecom"],
            ["id"=>"9ed35a32-7d6f-11e3-9830-5404a6144203","name"=>"Books and magazines"],
            ["id"=>"9ed44492-7d6f-11e3-98d1-5404a6144203","name"=>"Religion and spirituality (for profit)"],
            ["id"=>"9ed492ac-7d6f-11e3-a2d2-5404a6144203","name"=>"Sports and outdoors"],
            ["id"=>"9ed3f677-7d6f-11e3-96a2-5404a6144203","name"=>"Gifts and flowers"]
        ];

        foreach($data as $single){
            $businessCategory = new BusinessCategory();
            $businessCategory->name = $single['name'];
            $businessCategory->dwolla_key = $single['id'];
            $businessCategory->save();
        }
    }
}
