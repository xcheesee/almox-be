<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaidaFormRequest extends FormRequest
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
            //'departamento_id' => 'required',
            //'origem_id' => 'required',
            'local_servico_id' => 'required',
            'ordem_servico_id' => 'unique:saidas,ordem_servico_id'
        ];
    }

    public function messages()
    {
        return [
            'required' => "O campo ':attribute' é obrigatório",
            'ordem_servico_id.unique' => "Ordem de serviço ja cadastrada em uma saida."
        ];
    }
}
