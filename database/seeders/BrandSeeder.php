<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') !== 'production') {

            $brand = new Brand();
            $brand->name = 'Samsung';
            $brand->save();

            $brand = new Brand();
            $brand->name = 'Xiaomi';
            $brand->save();
        }
    }
}
