<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OcorrenciaFormRequest extends FormRequest
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
            'local_id' => 'required',
            'data_ocorrencia' => 'required|date',
            'tipo_ocorrencia' => 'required',
            'boletim_ocorrencia' => 'required',
            'justificativa' => 'required',
            'itens' => 'required|array',
        ];
    }
    
    public function messages()
    {
        return [
            'required' => "O campo ':attribute' é obrigatório",
            'date' => 'A Data da Ocorrencia deve ser preenchida corretamente!',
            'itens.array' => "No campo 'itens' deverá ser inserido uma lista"
        ];
    }
}
