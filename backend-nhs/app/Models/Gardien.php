<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Gardien extends Model
    {
        use HasFactory;

        protected $table = 'gardien';
        protected $primaryKey = 'id_gardien';
        public $timestamps = false;

        protected $fillable = [
            'id_personne',
        ];

        public function personne()
        {
            return $this->belongsTo(Personne::class, 'id_personne', 'id_personne');
        }
    }
?>


