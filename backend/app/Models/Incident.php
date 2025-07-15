<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Incident extends Model
    {
        use HasFactory;

        protected $table = 'incident';
        protected $primaryKey = 'id';
        public $timestamps = false;

        protected $fillable = [
            'datetime',
            'object',
            'statut',
            'id_signaleur',
            'pieces_jointes',
        ];

        protected $casts = [
            'datetime' => 'datetime',
            'pieces_jointes' => 'array',
        ];

        public function signaleur()
        {
            return $this->belongsTo(Personne::class, 'id_signaleur', 'id_personne');
        }
    }
?>


