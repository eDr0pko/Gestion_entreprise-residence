<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;

    protected $table = 'invite';
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean'
    ];

    protected $attributes = [
        'actif' => true
    ];

    /**
     * Relation avec la personne
     */
    public function personne()
    {
        return $this->belongsTo(Personne::class, 'email', 'email');
    }

    /**
     * Relation avec les visites
     */
    public function visites()
    {
        return $this->hasMany(Visite::class, 'email_invite', 'email');
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
     * Scope pour chercher par email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}
