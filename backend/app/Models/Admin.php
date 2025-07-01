<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;

    protected $fillable = [
        'email_personne',
        'mot_de_passe_admin',
    ];

    protected $hidden = [
        'mot_de_passe_admin',
    ];

    public function personne()
    {
        return $this->belongsTo(Personne::class, 'email_personne', 'email');
    }
}
