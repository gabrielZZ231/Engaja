<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atividade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'evento_id',
        'municipio_id',
        'descricao',
        'dia',
        'hora_inicio',
        'hora_fim',
        'presenca_ativa',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    public function presencas()
    {
        return $this->hasMany(Presenca::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }
}

