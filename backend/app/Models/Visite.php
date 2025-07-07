<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Visite extends Model
    {
        use HasFactory;

        protected $table = 'visite';
        protected $primaryKey = 'id_visite';
        public $timestamps = false;

        protected $fillable = [
            'id_invite',
            'email_visiteur',
            'motif_visite',
            'date_visite_start',
            'date_visite_end',
            'statut_visite'
        ];

        protected $casts = [
            'date_visite_start' => 'datetime',
            'date_visite_end' => 'datetime'
        ];

        protected $attributes = [
            'statut_visite' => 'programmee'
        ];

        /**
         * Relation avec l'invité
         */
        public function invite()
        {
            return $this->belongsTo(Invite::class, 'id_invite', 'id_personne');
        }

        /**
         * Relation avec la personne invitée
         */
        public function personne()
        {
            return $this->belongsTo(Personne::class, 'id_invite', 'id_personne');
        }

        /**
         * Scope pour les visites programmées
         */
        public function scopeProgrammees($query)
        {
            return $query->where('statut_visite', 'programmee');
        }

        /**
         * Scope pour les visites en cours
         */
        public function scopeEnCours($query)
        {
            return $query->where('statut_visite', 'en_cours');
        }

        /**
         * Scope pour les visites terminées
         */
        public function scopeTerminees($query)
        {
            return $query->where('statut_visite', 'terminee');
        }

        /**
         * Marquer la visite comme terminée
         */
        public function markAsCompleted()
        {
            $this->update(['statut_visite' => 'terminee']);
        }

        /**
         * Annuler la visite
         */
        public function cancel()
        {
            $this->update(['statut_visite' => 'annulee']);
        }
    }
?>


