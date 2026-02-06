<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiIndicador extends Model
{
    protected $table = 'bi_indicadores';

    protected $fillable = [
        'codigo',
        'nome',
        'unidade',
        'fonte',
        'descricao',
    ];
}

