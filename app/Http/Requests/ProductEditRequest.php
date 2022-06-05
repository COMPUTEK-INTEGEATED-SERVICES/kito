<?php

namespace App\Http\Requests;

use App\Enums\ProductConditions;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ProductEditRequest extends FormRequest
{
    use ResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'id'=>'required|integer|exists:products,id',
            'name'=>'required|string|max:255',
            'description'=>'required|string|max:255',
            'price'=>'required|numeric',
            'delivery_charge'=>'required|numeric',
            'condition'=>'required|in:'.strtolower(implode(',', $conditions)),
            'variation'=>'required|array',
            'variation.*'=>'required|string|max:255',
            'available_in_state'=>'required|integer|exists:states,id'
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
