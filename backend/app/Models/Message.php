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
            'id_auteur',
            'contenu_message',
            'date_envoi',
            'a_fichiers',
            'reply_to', // Ajouté pour la citation
        ];

        protected $casts = [
            'date_envoi' => 'datetime',
            'a_fichiers' => 'boolean',
        ];

        // Relations
        public function groupe()
        {
            return $this->belongsTo(GroupeMessage::class, 'id_groupe_message', 'id_groupe_message');
        }

        public function auteur()
        {
            return $this->belongsTo(Personne::class, 'id_auteur', 'id_personne');
        }

        public function fichiers()
        {
            return $this->hasMany(MessageFichier::class, 'id_message', 'id_message');
        }

        public function reactions()
        {
            return $this->hasMany(MessageReaction::class, 'id_message', 'id_message');
        }

        // Relation vers le message cité
        public function replyTo()
        {
            return $this->belongsTo(Message::class, 'reply_to', 'id_message');
        }
    }
?>


