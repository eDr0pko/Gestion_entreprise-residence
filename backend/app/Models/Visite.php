<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visite extends Model
{
    protected $table = 'visite';
    protected $primaryKey = 'id_visite';
    public $timestamps = false;
    protected $fillable = [
        'email_visiteur',
        'id_invitation',
        'motif_visite',
        'date_visite',
        'statut_visite'
    ];
}

