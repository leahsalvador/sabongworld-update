<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\CurrentPasswordCheckRule;
use Illuminate\Validation\Rule;

class WalletRequest extends FormRequest
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
        $rules = [
            'transaction_type' => ['required', 'string'],
            'load_to' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
        ];
        $load_to = User::where('id', $this->request->get('load_to'))->first();

        if ($user->user_level == 'admin') {
            $rules['amount'][] = ($this->input('transaction_type') == 'deposit') ? 'gt:0' : 'lte:' . $load_to->wallet->points;
        } else if ($user->user_level == 'super-admin') {
            $rules['amount'][] = ($this->input('transaction_type') == 'deposit') ? 'gt:0' : 'lte:' . $load_to->wallet->points;
        } else {
            $rules['transaction_type'][] = ($load_to->user_level == 'sub-agent') ? Rule::in(['deposit', 'withdraw']) : Rule::in(['deposit', 'withdraw']);
            $rules['amount'][] = ($this->input('transaction_type') == 'deposit') ? 'lte:' . $user->wallet->points : 'lte:' . $load_to->wallet->points;
        }

        return $rules;
    }
}
