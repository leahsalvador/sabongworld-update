<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\CurrentPasswordCheckRule;

class ComissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = Auth::user();
        $rules =  [
            'transaction_type' => ['required', 'string'],
            'load_to' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'password' => ['required', 'min:6', new CurrentPasswordCheckRule]
        ];
        $load_to = User::where('id',$this->request->get('load_to'))->first();
        
         $rules['amount'][] = ($this->input('transaction_type') == 'deposit') ? 'lte:'.$user->wallet->comission : 'lte:'.$load_to->wallet->comission;
         
         return $rules;
    }
}
