<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PokemonMove extends Model
{
    protected $fillable = [
        'pokemon_id', 'move_name', 'move_type',
        'damage_class', 'power', 'accuracy', 'learn_method',
    ];
}