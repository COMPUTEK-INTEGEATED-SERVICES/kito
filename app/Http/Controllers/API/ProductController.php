<?php


namespace App\Http\Controllers\API;


use App\Actions\ProductActions;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductEditRequest;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductController extends Controller
{
    private ?\Illuminate\Contracts\Auth\Authenticatable $user;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->user = auth()->guard('sanctum')->user();
    }

    public function create(ProductCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        $product = new ProductActions();
        try {
            $product->create($request);
            return $this->successResponse();
        }catch (\Throwable $throwable){
            return $this->errorResponse();
        }
    }

    public function edit(ProductEditRequest $request): \Illuminate\Http\JsonResponse
    {
        $product = new ProductActions();
        try {
            $product->edit($request);
            return $this->successResponse();
        }catch (\Throwable $throwable){
            return $this->errorResponse();
        }
    }

    public function addProductImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $v = Validator::make($request->all(), [
            'product_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($v->fails()) {
            return $this->validationErrorResponse($v->errors());
        }
        $product = Products::find($request->product_id);

        if($request->hasFile('image') && $request->file('image')->isValid()){
            $product
                ->addMediaFromRequest('image')
                ->toMediaCollection('product_images');
        }

        return $this->successResponse();
    }

    public function deleteProductImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $v = Validator::make( $request->all(), [
            'image_id'=>'required|string|exists:media,id',
        ]);

        if($v->fails()){
            return $this->validationErrorResponse($v->errors(), 'Validation failed');
        }

        $productAction = new ProductActions();
        try {
            $product = Products::find(Media::find($request->image_id)->model_id);
            if ($product && $this->user->can('interact', $product)){
                if($productAction->deleteImage($request, $product))
                    return $this->successResponse([], 'Image deleted successfully');
            }
        }catch (\Throwable $throwable){
            //report($throwable);
        }

        return $this->errorResponse();
    }

    public function delete(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($this->user->can('interact', Products::find($request->id))){
            try {
                ProductActions::delete($request->id);
                return $this->successResponse([], 'Product deleted successfully');
            }catch (\Throwable $throwable){
                //report($throwable);
            }
        }
        return $this->errorResponse([], 'An error occurred');
    }
}
