<?php

namespace App\Repositories\Traits;
use App\Models\Store\Product;
use App\Models\Store\Products\Variant;
use App\Models\Store\Products\VariantInventory;
use Illuminate\Http\Request;
trait Products{
    /**
     * Add products to a store
     */
    public function addProducts(Request $data, $store, $productId = null){
        $productHelper = new Product;

        //require auth to create a product
        $this->userHasAccess($store);

        //extract only the data needed for product
        $productData = $data->except('productId', 'step', 'variation');

        //create product
        if(!$productId){
            $product = $productHelper::create($productData);
        }else{
            $didUpdate = $productHelper::whereId($productId)
                            ->update($productData);
            if($didUpdate) $product = $productHelper::find($productId);
        }

        //if variants exists, work on 'em
        if($data->has('variation')){
            $this->createVariants($data->only('variation'), $product);
        }

        //attach product to store
        $store->products()->save($product);

        //attach variant and inventory relationship to response
        $product->load('variant.inventory');

        return $product;
    }

    /**
     * Manages how products are attached to variants
     * and variants inventory.
     * 1. We store the different variant values and their type in the db
     * 2. We get the individual variant values and their stock w/ price
     * and save to the db.
     */
    public function createVariants($variants, $product){
        //these will always return array
        $variantTypes = $variants['variation']['variations'];
        $variantInventory = $variants['variation']['inventory'];

        //first create the parent variant(s)
        foreach($variantTypes as $variantType){
            $variant = Variant::create([
                'type' => $variantType['type'],
                'values' => $variantType['tags'],
                'product_id' => $product->id
            ]);

            //add inventory
            foreach($variantInventory as $vInventory){
                //now add variant inventory
                $inventory = VariantInventory::create([
                    'variant_id' => $variant->id,
                    'variant' => $vInventory['variant'],
                    'stock' => $vInventory['stock'],
                    'price' => $vInventory['price'],
                    'media' => '[]'
                ]);

                //attach the variant_id to inventory
                $variant->inventory()->save($inventory);
            }
        }

    }

    public function updateProduct($data, $productId){
        $product = Product::where('id', $productId)
                            ->update($data);

        if($product) return true;
    }
}
