<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') !== 'production') {

            for ($x = 1; $x <= 10; $x++) {

                $product = new Supplier();
                $product->name = 'SUPPLIER ' . $x;
                $product->save();
            }
        }
    }
}
