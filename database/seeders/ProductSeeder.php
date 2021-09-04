<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') !== 'production') {

            $colors = ['BLACK', 'BLUE', 'GREY'];
            $brand = Brand::query()->first();

            for ($x = 1; $x <= 10; $x++) {

                foreach ($colors as $i => $color) {
                    dump('Voy a crear');
                    $product = new Product();
                    $product->model = 'MD' . $x;
                    $product->description = 'MD' . $x . ' ' . $color;
                    $product->brand_id = $brand->id;
                    $product->save();
                    dump('Listo');
                }
            }
        }
    }
}
