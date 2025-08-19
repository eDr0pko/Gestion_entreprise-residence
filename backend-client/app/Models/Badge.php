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
            'droit',
            'statut',
            'zone_acces',
            'niveau_securite',
            'date_creation',
            'date_derniere_utilisation'
        ];

        protected $casts = [
            'numero' => 'integer',
            'id_utilisateur' => 'integer',
            'date_creation' => 'datetime',
            'date_derniere_utilisation' => 'datetime'
        ];

        // Relations
        public function utilisateur()
        {
            return $this->belongsTo(Personne::class, 'id_utilisateur', 'id_personne');
        }

        public function suivis()
        {
            return $this->hasMany(SuiviBadge::class, 'id_badge', 'numero')
                        ->orderBy('date_heure', 'desc');
        }

        // Accesseurs pour améliorer les données
        public function getUtilisateurNomCompletAttribute()
        {
            return $this->utilisateur ? $this->utilisateur->nom . ' ' . $this->utilisateur->prenom : 'Aucun';
        }

        public function getStatutActuelAttribute()
        {
            // Si on a un statut direct, on l'utilise
            if ($this->statut) {
                return $this->statut;
            }

            // Sinon, on détermine le statut depuis l'historique
            $dernierSuivi = $this->suivis()->first();
            if (!$dernierSuivi) {
                return 'en_attente';
            }
            
            switch ($dernierSuivi->action) {
                case 'activation':
                case 'utilisation':
                    return 'actif';
                case 'desactivation':
                case 'désactivation':
                    return 'inactif';
                case 'suspension':
                    return 'suspendu';
                default:
                    return 'inconnu';
            }
        }

        public function getDerniereUtilisationAttribute()
        {
            $derniereUtilisation = $this->suivis()
                ->where('action', 'utilisation')
                ->first();
            
            return $derniereUtilisation ? $derniereUtilisation->date_heure : null;
        }
    }
?>


