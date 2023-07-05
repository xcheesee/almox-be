<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdemServicoFormRequest extends FormRequest
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
            'origem_id' => 'required',
            'local_servico_id' => 'required',
            //'data_inicio_servico' => 'required',
            //'almoxarife_nome' => 'required',
            //'almoxarife_email' => 'required|email',
            'user_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => "O campo ':attribute' é obrigatório",
            'email' => "O campo ':attribute' precisa ser um e-mail válido",
        ];
    }
}
