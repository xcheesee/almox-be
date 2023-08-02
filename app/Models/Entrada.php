<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Kyslik\ColumnSortable\Sortable;

class Entrada extends Model
{
    use HasFactory;
    use Sortable;

    protected $fillable = [
        'departamento_id',
        'tipo_item_id',
        'local_id',
        'data_entrada',
        'processo_sei',
        'numero_contrato',
        'numero_nota_fiscal',
        'arquivo_nota_fiscal',
        'ativo',
    ];

    public $sortable = ['id','data_entrada','processo_sei','numero_contrato','numero_nota_fiscal'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function getDataEntradaFormatadaAttribute(){
        if ($this->data_entrada){
            $date = Carbon::parse($this->data_entrada);
            return $date->format("d/m/Y");
        }

        return null;
    }

    public function getProcessoSeiFormatadoAttribute() {
        if ($this->processo_sei){
            $str = $this->processo_sei;
            return substr($str,0,4).'.'.substr($str,4,4).'/'.substr($str,8,7).'-'.substr($str,15,1);
        }

        return null;
    }

    public function getNumeroContratoFormatadoAttribute() {
        if ($this->numero_contrato){
            $str = $this->numero_contrato;
            return substr($str,0,3).'/'.substr($str,3,4).'/'.substr($str,7,4);
        }

        return null;
    }

    public function scopeEntradaDepoisDe(Builder $query, $date): Builder
    {
        return $query->where('data_entrada', '>=', Carbon::parse($date));
    }

    public function scopeEntradaAntesDe(Builder $query, $date): Builder
    {
        return $query->where('data_entrada', '<=', Carbon::parse($date));
    }

    public function getArquivoNotaFiscalUrlAttribute(){
        if ($this->arquivo_nota_fiscal){
            return Storage::url($this->arquivo_nota_fiscal);
        }
        return null;
    }
}
