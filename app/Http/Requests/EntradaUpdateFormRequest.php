<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntradaUpdateFormRequest extends FormRequest
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
            'departamento_id' => 'required',
            'local_id' => 'required',
            'processo_sei' => 'required',
            'numero_contrato' => 'required',
            'numero_nota_fiscal' => 'required',
            //'arquivo_nota_fiscal' => 'required|mimes:png,jpg,jpeg,gif,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'required' => "O campo ':attribute' é obrigatório",
            'arquivo_nota_fiscal.mimes' => "Tipos de arquivos permitidos para upload: 'png','jpg','jpeg','gif','pdf'",
        ];
    }
}
