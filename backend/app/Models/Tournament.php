<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    // Si le nom de ta table est bien "tournaments", ce n’est pas obligatoire.
    protected $table = 'tournaments';

    // Désactive la protection des champs si tu veux utiliser $tournament->create([...])
    protected $guarded = [];

    // Si ta table n’a pas de timestamps (created_at, updated_at), ajoute ceci :
    public $timestamps = false;
}
