<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class MessageReaction extends Model
    {
        use HasFactory;

        protected $table = 'message_reaction';
        protected $primaryKey = 'id_reaction';
        public $timestamps = false;

        protected $fillable = [
            'id_message',
            'id_personne',
            'emoji',
            'date_reaction',
        ];

        protected $casts = [
            'date_reaction' => 'datetime',
        ];

        // Relations
        public function message()
        {
            return $this->belongsTo(Message::class, 'id_message', 'id_message');
        }

        public function personne()
        {
            return $this->belongsTo(Personne::class, 'id_personne', 'id_personne');
        }
    }
?>


