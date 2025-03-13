<?php

namespace Database\Seeders;

use App\Models\WebCategory;
use App\Models\WebSubcategory;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WebCategory::factory(10)->create();
        WebSubcategory::factory(10)->create();
    }
}
