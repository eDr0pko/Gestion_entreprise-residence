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
            'message'
        ];

        protected $casts = [
            'id' => 'integer',
            'id_badge' => 'integer',
            'date_heure' => 'datetime'
        ];

        protected $dates = [
            'date_heure'
        ];

        /**
         * Relation avec le badge
         */
        public function badge()
        {
            return $this->belongsTo(Badge::class, 'id_badge', 'numero');
        }

        /**
         * Scope pour trier par date décroissante
         */
        public function scopeRecent($query)
        {
            return $query->orderBy('date_heure', 'desc');
        }

        /**
         * Scope pour filtrer par action
         */
        public function scopeParAction($query, $action)
        {
            return $query->where('action', $action);
        }

        /**
         * Actions possibles pour les badges
         */
        public static function getActionsDisponibles()
        {
            return [
                'activation' => 'Activation',
                'desactivation' => 'Désactivation',
                'suspension' => 'Suspension',
                'utilisation' => 'Utilisation',
                'creation' => 'Création',
                'modification' => 'Modification',
                'attribution' => 'Attribution',
                'retrait' => 'Retrait'
            ];
        }

        /**
         * Obtient le libellé de l'action
         */
        public function getActionLabelAttribute()
        {
            $actions = self::getActionsDisponibles();
            return $actions[$this->action] ?? ucfirst($this->action);
        }

        /**
         * Alias pour getActionLabelAttribute (compatibilité frontend)
         */
        public function getActionLibelleAttribute()
        {
            return $this->getActionLabelAttribute();
        }

        /**
         * Obtient la couleur associée à l'action
         */
        public function getActionColorAttribute()
        {
            $colors = [
                'activation' => 'green',
                'désactivation' => 'red',
                'desactivation' => 'red',
                'suspension' => 'orange',
                'utilisation' => 'blue',
                'creation' => 'emerald',
                'création' => 'emerald',
                'modification' => 'orange',
                'attribution' => 'purple',
                'retrait' => 'red',
                'suppression' => 'red'
            ];

            return $colors[$this->action] ?? 'gray';
        }
    }
?>


