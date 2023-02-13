<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Models\Transaction;
use App\Rules\CurrentPasswordCheckRule;
use Illuminate\Validation\Rule;

class WithdrawalRequest extends FormRequest
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
            'transaction_id' => ['required', 'numeric'],
            'type' => ['required', 'string'],
            'amount' => ['required','numeric'],
        ];
        // $load_to = Transaction::where('id',$this->request->get('transaction_id'))->first();
        //  $rules['amount'][] = ($this->input('type') == 'Reject') ? 'gt:0': 'lte:'.$user->wallet->points;
         return $rules;
    }
}
