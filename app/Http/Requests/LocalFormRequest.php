<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocalFormRequest extends FormRequest
{
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
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required',
            'tipo' => 'required|in:base,parque,autarquia,secretaria,subprefeitura',
        ];
    }

    public function messages()
    {
        return [
            'required' => "O campo ':attribute' é obrigatório",
            'tipo.in' => "Valores possíveis para tipo: 'base','parque','autarquia','secretaria','subprefeitura'",
        ];
    }
}
