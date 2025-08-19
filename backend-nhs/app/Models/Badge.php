<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Badge extends Model
    {
        use HasFactory;

        protected $table = 'badge';
        protected $primaryKey = 'numero';
        public $timestamps = false;

        protected $fillable = [
            'id_utilisateur',
            'commentaire',
            'droit'
        ];

        protected $casts = [
            'numero' => 'integer',
            'id_utilisateur' => 'integer'
        ];

        /**
         * Relation avec la personne qui possède le badge
         */
        public function personne()
        {
            return $this->belongsTo(Personne::class, 'id_utilisateur', 'id_personne');
        }

        /**
         * Alias pour la relation personne (pour compatibilité frontend)
         */
        public function utilisateur()
        {
            return $this->belongsTo(Personne::class, 'id_utilisateur', 'id_personne');
        }

        /**
         * Relation avec l'historique de suivi du badge
         */
        public function suivis()
        {
            return $this->hasMany(SuiviBadge::class, 'id_badge', 'numero')->orderBy('date_heure', 'desc');
        }

        /**
         * Relation avec le dernier suivi
         */
        public function dernierSuivi()
        {
            return $this->hasOne(SuiviBadge::class, 'id_badge', 'numero')->latest('date_heure');
        }

        /**
         * Scope pour les badges actifs
         */
        public function scopeActifs($query)
        {
            return $query->whereHas('dernierSuivi', function($q) {
                $q->where('action', '!=', 'desactivation');
            });
        }

        /**
         * Vérifie si le badge est actif
         */
        public function getIsActifAttribute()
        {
            $dernierSuivi = $this->dernierSuivi;
            return $dernierSuivi ? $dernierSuivi->action !== 'desactivation' : true;
        }

        /**
         * Obtient le statut actuel du badge (compatible frontend)
         */
        public function getStatutActuelAttribute()
        {
            $dernierSuivi = $this->dernierSuivi;
            if (!$dernierSuivi) {
                return 'nouveau';
            }
            
            switch ($dernierSuivi->action) {
                case 'activation':
                    return 'actif';
                case 'désactivation':
                case 'desactivation':
                    return 'inactif';
                case 'suspension':
                    return 'suspendu';
                default:
                    return 'actif';
            }
        }

        /**
         * Obtient le statut actuel du badge
         */
        public function getStatutAttribute()
        {
            return $this->getStatutActuelAttribute();
        }

        /**
         * Obtient la dernière utilisation du badge
         */
        public function getDerniereUtilisationAttribute()
        {
            $dernierUtilisation = $this->suivis()
                ->where('action', 'utilisation')
                ->first();
            
            return $dernierUtilisation ? $dernierUtilisation->date_heure : null;
        }
    }
?>


