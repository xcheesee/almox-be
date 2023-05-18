<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferenciaFormRequest extends FormRequest
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
            'base_origem_id' => 'required',
            'base_destino_id' => 'required',
            'data_transferencia' => 'required|date',
            'status' => 'required',
            'itens' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'required' => "O campo ':attribute' é obrigatório",
            'date' => "A Data de Transferencia deve ser preenchida corretamente!",
            'intes.array' => "No campo 'itens' deverá ser inserido uma lista"
        ];
    }
}