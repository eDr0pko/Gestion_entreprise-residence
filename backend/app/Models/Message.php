<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Message extends Model
    {
        use HasFactory;

        protected $table = 'message';
        protected $primaryKey = 'id_message';
        public $timestamps = false;

        protected $fillable = [
            'id_groupe_message',
            'email_auteur',
            'contenu_message',
            'date_envoi',
        ];

        protected $casts = [
            'date_envoi' => 'datetime',
        ];

        // Relations
        public function groupe()
        {
            return $this->belongsTo(GroupeMessage::class, 'id_groupe_message', 'id_groupe_message');
        }

        public function auteur()
        {
            return $this->belongsTo(Personne::class, 'email_auteur', 'email');
        }
    }
?>


