<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    use HasFactory;

    protected $table = 'ban';
    protected $primaryKey = 'id_ban';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_proprietaire',
        'motif',
    ];

    public function personne()
    {
        return $this->belongsTo(Personne::class, 'id_proprietaire', 'id_personne');
    }
}
