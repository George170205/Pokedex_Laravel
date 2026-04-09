<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemon';

    protected $fillable = [
        'pokedex_id', 'generation', 'name', 'name_es', 'sprite',
        'sprite_animated', 'hp', 'attack', 'defense', 'sp_attack',
        'sp_defense', 'speed', 'height', 'weight', 'color',
        'description', 'evolution_chain_url',
    ];

    public function types()
    {
        return $this->hasMany(PokemonType::class)->orderBy('slot');
    }

    public function moves()
    {
        return $this->hasMany(PokemonMove::class);
    }

    public function getTypeListAttribute()
    {
        return $this->types->pluck('type')->toArray();
    }
}