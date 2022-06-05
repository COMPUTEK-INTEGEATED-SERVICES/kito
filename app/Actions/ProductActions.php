<?php


namespace App\Actions;


use App\Models\Product;
use App\Models\Products;
use App\Models\ProductVariantRelation;
use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Exception;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductActions
{
    public function create($data): bool
    {
        DB::beginTransaction();
        try {
            $product = Products::create([
                'name'=>$data->name,
                'description'=>$data->description,
                'price'=>$data->price,
                'delivery_charge'=>$data->delivery_charge,
                'condition'=>$data->condition
            ]);

            if ($data->image){
                $product->addMultipleMediaFromRequest(['images'])
                    ->each(function ($fileAdder) {
                        $fileAdder
                            ->toMediaCollection('product_images');
                    });
            }

            //create a sku
            $sku = Sku::create([
                'name'=>strtolower(str_replace(' ', '_', $product->name).time()),
                'product_id'=>$product->id,
                'price'=>$product->price
            ]);

            //add product variants
            foreach ($data->variation as $key=>$value){
                ProductVariantRelation::create([
                    'product_id'=>$product->id,
                    'variant_id'=>$key,
                    'sku_id'=>$sku->id,
                    'variant_value'=>$value
                ]);
            }
            DB::commit();
            return true;
        }catch (\Throwable $throwable){
            DB::rollBack();
            return false;
        }
    }

    public function edit($data)
    {
        DB::beginTransaction();
        try {
            $product = Products::find($data->id);
            $product->name = $data->name;
            $product->description = $data->description;
            $product->price = $data->price;
            $product->delivery_charge = $data->delivery_charge;
            $product->condition = $data->condition;
            $product->save();

            DB::commit();
            return true;
        }catch (\Throwable $throwable){
            report($throwable);
            DB::rollBack();
            return false;
        }
    }

    public function deleteImage(Request $request, Products $product): bool
    {
        try {
            $media = Media::find($request->image_id);

            $model_type = $media->model_type;

            $model = $model_type::find($media->model_id);
            $model->deleteMedia($media->id);

            return true;
        }catch (\Throwable $throwable){
            report($throwable);
            return false;
        }
    }

    public static function delete($id): bool
    {
        try {
            return Products::find($id)->delete();
        }catch (\Throwable $throwable){
            report($throwable);
            return false;
        }
    }
}
