<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class SuiviBadge extends Model
    {
        use HasFactory;

        protected $table = 'suivi_badge';
        protected $primaryKey = 'id';
        public $timestamps = false;

        protected $fillable = [
            'date_heure',
            'id_badge',
            'action',
            'message',
            'id_utilisateur_action',
            'details_technique'
        ];

        protected $casts = [
            'id' => 'integer',
            'id_badge' => 'integer',
            'id_utilisateur_action' => 'integer',
            'date_heure' => 'datetime',
            'details_technique' => 'array'
        ];

        // Relations
        public function badge()
        {
            return $this->belongsTo(Badge::class, 'id_badge', 'numero');
        }

        public function utilisateurAction()
        {
            return $this->belongsTo(Personne::class, 'id_utilisateur_action', 'id_personne');
        }

        // Accesseurs
        public function getActionLibelleAttribute()
        {
            $libelles = [
                'activation' => 'Activation',
                'desactivation' => 'Désactivation',
                'désactivation' => 'Désactivation',
                'utilisation' => 'Utilisation',
                'modification' => 'Modification',
                'creation' => 'Création',
                'création' => 'Création',
                'suppression' => 'Suppression',
                'suspension' => 'Suspension',
                'reactivation' => 'Réactivation'
            ];

            return $libelles[$this->action] ?? ucfirst($this->action);
        }

        public function getActionColorAttribute()
        {
            $colors = [
                'activation' => 'green',
                'desactivation' => 'red',
                'désactivation' => 'red',
                'utilisation' => 'blue',
                'modification' => 'orange',
                'creation' => 'emerald',
                'création' => 'emerald',
                'suppression' => 'red',
                'suspension' => 'yellow',
                'reactivation' => 'green'
            ];

            return $colors[$this->action] ?? 'gray';
        }
    }
?>


