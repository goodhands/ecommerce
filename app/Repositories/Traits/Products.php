<?php

namespace App\Repositories\Traits;

use App\Models\Store\Product;
use App\Models\Store\Products\Variant;
use App\Models\Store\Products\VariantInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait Products
{
    /**
     * Add products to a store
     */
    public function addProducts(Request $data, $store, $productId = null)
    {
        //require auth to create a product
        $this->userHasAccess($store);

        //extract only the data needed for product
        $productData = $data->except('productId', 'step', 'variation');

        //create product
        if (!$productId) {
            $product = Product::create($productData);
        } else {
            $didUpdate = Product::whereId($productId)
                            ->update($productData);
            if ($didUpdate) $product = Product::find($productId);
        }

        //if variants exists, work on 'em
        if ($data->has('variation') && !empty($data->variation['variations'])) {
            $this->createVariants($data->only('variation'), $product);
        }

        //attach product to store
        $store->products()->save($product);

        //attach variant, inventory and variantInventory relationship to response
        $product->load('variant', 'inventory', 'variantInventory');

        return $product;
    }

    /**
     * Manages how products are attached to variants
     * and variants inventory.
     * 1. We store the different variant values and their type in the db
     * 2. We get the individual variant values and their stock w/ price
     * and save to the db.
     */
    public function createVariants($variants, $product)
    {
        //these will always return array
        $variantTypes = $variants['variation']['variations'];
        $variantInventory = $variants['variation']['inventory'];

        //first create the parent variant(s)
        foreach($variantTypes as $variantType){
            //reusable data to verify if variant exists
            $verifyVariant = [
                'product_id' => $product->id,
                'type' => $variantType['type'],
            ];

            //first check if the variant already exists
            $exists = Variant::where($verifyVariant)->exists();

            if($exists){
                Log::debug(print_r($variantType, true) . " variant exists so we will update it ");
                //if it already, exists update the values
                $variant = Variant::where($verifyVariant)->update([
                    'values' => $variantType['values'],
                    'type' => $variantType['type'],
                ]);
            }else{
                Log::debug(print_r($variantType, true) . " variant does not exist so we will create it ");
                $variant = Variant::create([
                    'type' => $variantType['type'],
                    'values' => $variantType['values'],
                    'product_id' => $product->id
                ]);
            }
        }

        // add inventory, Inventory is independent of variants
        // because they contain enough information needed about
        // the variant(s) they are referencing
        foreach ($variantInventory as $vInventory) {
            //check if variant-inventory exists before adding
            $inventoryExists = $vInventory['id'] ?? false;

            if ($inventoryExists) {
                Log::debug(print_r($vInventory, true) . " inventory exist so we will update it ");

                //if it already exists, the data we wanna update is
                //probably the stock, price or media
                VariantInventory::where($vInventory)
                                            ->update([
                                                'stock' => $vInventory['stock'],
                                                'price' => $vInventory['price'],
                                                'media' => '[]',
                                                'variant' => $vInventory['variant'],
                                            ]);
            }else{
                Log::debug(print_r($vInventory, true) . " inventory does not exist so we will create it ");

                //now add variant inventory
                VariantInventory::create([
                    'variant' => $vInventory['variant'],
                    'product_id' => $product->id,
                    'stock' => $vInventory['stock'],
                    'price' => $vInventory['price'],
                    'media' => '[]'
                ]);
            }
        }

    }

    public function updateProduct($data, $productId){
        $product = Product::where('id', $productId)
                            ->update($data);

        if($product) return true;
    }
}
