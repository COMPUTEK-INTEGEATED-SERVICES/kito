<?php

namespace App\Http\Requests;

use App\Actions\ProductActions;
use App\Enums\ProductConditions;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProductCreateRequest extends FormRequest
{
    use ResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $conditions = [ProductConditions::NEW, ProductConditions::USED];
        return [
            'name'=>'require|string',
            'category_id'=>'required|int|exists:categories,id',
            'sub_category_id'=>'required|int|exists:categories,id',
            'description'=>'required|string',
            'images'=>'required|array|max:5',
            'images.*'=>'mimes:jpeg,png,jpg',
            'price'=>'required|numeric',
            'delivery_charge'=>'required|numeric',
            'variation'=>'required|array',
            'variation.*'=>'exists:variants,id',
            'variation.*.*'=>'string',
            'available_in_state'=>'int|exists:states,id',
            'condition'=>'required|in:'.strtolower(implode(',', $conditions))
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if($this->wantsJson())
        {
            $response = $this->validationErrorResponse($validator->errors());
        }else{
            $response = redirect()
                ->route('guest.login')
                ->with('message', 'Ops! Some errors occurred')
                ->withErrors($validator);
        }

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
