<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;

    protected $table = 'invite';
    protected $primaryKey = 'id_personne';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_personne',
        'actif',
        'date_inscription',
        'date_expiration',
        'invite_par',
        'commentaire',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'date_inscription' => 'datetime',
        'date_expiration' => 'datetime',
    ];

    protected $attributes = [
        'actif' => true
    ];

    /**
     * Relation avec la personne
     */
    public function personne()
    {
        return $this->belongsTo(Personne::class, 'id_personne', 'id_personne');
    }

    /**
     * Relation avec la personne qui a invité
     */
    public function invitePar()
    {
        return $this->belongsTo(Personne::class, 'invite_par', 'id_personne');
    }

    /**
     * Relation avec les visites
     */
    public function visites()
    {
        return $this->hasMany(Visite::class, 'id_invite', 'id_personne');
    }

    /**
     * Vérifier si l'invité est actif
     */
    public function isActive()
    {
        return $this->actif;
    }

    /**
     * Désactiver l'invité
     */
    public function deactivate()
    {
        $this->update(['actif' => false]);
    }

    /**
     * Activer l'invité
     */
    public function activate()
    {
        $this->update(['actif' => true]);
    }

    /**
     * Scope pour les invités actifs
     */
    public function scopeActive($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour chercher par id_personne
     */
    public function scopeByPersonneId($query, $id_personne)
    {
        return $query->where('id_personne', $id_personne);
    }
}
