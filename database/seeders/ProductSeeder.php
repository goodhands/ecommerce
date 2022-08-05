<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use App\Models\Store\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private function collectionId($store)
    {
        if (count($store->collections) > 1) {
            return $store->collections()->inRandomOrder()->first()->id;
        }

        return 1;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $store = Store::find(3);

        $http = Http::get("https://simplekart.postman.com/items?list=20");
        $products = $http->json();

        foreach ($products as $key => $product) {
            $data = array(
                "name" => $product["name"],
                "price" => $product["price"],
                "description" => $product["description"],
                "stock" => rand(0, 10),
                "sku" => strtoupper(
                    Str::replace(
                        array(" ", "-", "."),
                        array("_", rand(0, 6)),
                        Str::limit($product["name"], 6)
                    )
                ),
                "discount" => rand($product["price"] % $product["price"] * 3, $product["price"]),
                "collection_id" => $this->collectionId($store),
                "media_library" => array(
                    "https://source.unsplash.com/random/1200x800?sig=" . ($key + rand(0, 3)) . "&" . Str::slug($product["name"]),
                    "https://source.unsplash.com/random/1200x800?sig=" . ($key + rand(0, 3)) . "&" . Str::slug($product["name"]),
                    "https://source.unsplash.com/random/1200x800?sig=" . ($key + rand(0, 3)) . "&" . Str::slug($product["name"]),
                ),
                "shortname" => Str::slug($product["name"]),
                "status" => "published",
                "views" => rand(1000, 90000)
            );

            $product = Product::create($data);

            $store->products()->save($product);
        }
    }
}
