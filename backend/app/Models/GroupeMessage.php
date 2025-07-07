<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class GroupeMessage extends Model
    {
        use HasFactory;

        protected $table = 'groupe_message';
        protected $primaryKey = 'id_groupe_message';
        public $timestamps = false;

        protected $fillable = [
            'nom_groupe',
            'date_creation',
        ];

        protected $casts = [
            'date_creation' => 'datetime',
        ];

        // Relations
        public function personnes()
        {
            return $this->belongsToMany(Personne::class, 'personne_groupe', 'id_groupe_message', 'id_personne')
                        ->withPivot('date_adhesion', 'derniere_connexion');
        }

        public function messages()
        {
            return $this->hasMany(Message::class, 'id_groupe_message', 'id_groupe_message');
        }

        public function dernierMessage()
        {
            return $this->hasOne(Message::class, 'id_groupe_message', 'id_groupe_message')
                        ->orderBy('date_envoi', 'desc');
        }
    }
?>