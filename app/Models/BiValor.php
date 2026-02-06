<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiValor extends Model
{
    protected $fillable = [
        'indicador_id',
        'municipio_id',
        'ano',
        'valor_numeric',
        'valor_text',
    ];
}
