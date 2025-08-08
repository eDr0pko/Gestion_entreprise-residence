<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Admin extends Model
    {
        use HasFactory;

        protected $table = 'admin';
        protected $primaryKey = 'id_admin';
        public $timestamps = false;

        protected $fillable = [
            'id_personne',
            'niveau_acces',
            'date_nomination',
        ];

        public function personne()
        {
            return $this->belongsTo(Personne::class, 'id_personne', 'id_personne');
        }
    }
?>


