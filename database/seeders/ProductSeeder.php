<?php

namespace Database\Seeders;

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

            for ($x = 1; $x <= 10; $x++) {

                foreach ($colors as $i => $color) {

                    $product = new Product();
                    $product->upc = '0000' . $i . $x;
                    $product->model = 'MD' . $x;
                    $product->description = 'MD' . $x . ' ' . $color;
                    $product->save();
                }
            }
        }
    }
}
