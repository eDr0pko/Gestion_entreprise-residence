<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Personne extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'personne';
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'nom',
        'prenom',
        'mot_de_passe',
        'numero_telephone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mot_de_passe' => 'hashed',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * Relations
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'email_personne', 'email');
    }

    public function gardien()
    {
        return $this->hasOne(Gardien::class, 'email_personne', 'email');
    }

    public function resident()
    {
        return $this->hasOne(Resident::class, 'email_personne', 'email');
    }

    public function groupes()
    {
        return $this->belongsToMany(GroupeMessage::class, 'personne_groupe', 'email_personne', 'id_groupe_message')
                    ->withPivot('date_adhesion', 'derniere_connexion');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'email_auteur', 'email');
    }

    /**
     * Déterminer le rôle de la personne
     */
    public function getRole()
    {
        if ($this->admin) {
            return 'admin';
        } elseif ($this->gardien) {
            return 'gardien';
        } elseif ($this->resident) {
            return 'resident';
        }
        return null;
    }

    /**
     * Vérifier si la personne a un rôle spécifique
     */
    public function hasRole($role)
    {
        return $this->getRole() === $role;
    }

    // Accesseur pour le nom complet
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
