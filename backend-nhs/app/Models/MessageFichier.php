<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class MessageFichier extends Model
    {
        use HasFactory;

        protected $table = 'message_fichier';
        protected $primaryKey = 'id_fichier';
        public $timestamps = false;

        protected $fillable = [
            'id_message',
            'nom_fichier',
            'nom_original',
            'chemin_fichier',
            'type_fichier',
            'taille_fichier',
            'date_upload',
        ];

        protected $casts = [
            'date_upload' => 'datetime',
            'taille_fichier' => 'integer',
        ];

        // Relations
        public function message()
        {
            return $this->belongsTo(Message::class, 'id_message', 'id_message');
        }
    }
?>


