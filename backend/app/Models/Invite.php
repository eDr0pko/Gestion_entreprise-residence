<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $table = 'invite'; // car pas le nom standard "invites"

    protected $fillable = [
        'email_invite',
        'id_ban',
        'photo_invite',
        'date_invitation',
        'statut_invitation',
    ];

    public $timestamps = false; // si ta table n'a pas les colonnes created_at / updated_at
}

