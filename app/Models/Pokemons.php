<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemons extends Model
{
    use HasFactory;

    /**
     * Atributos em massa permitidos.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'tipo',
        'altura',
        'peso',
        'external_id',
        'foto',
        'ativo'
    ];
}
