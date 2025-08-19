<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiTokens;

    class Personne extends Authenticatable
    {
        use HasApiTokens, HasFactory, Notifiable;

        protected $table = 'personne';
        protected $primaryKey = 'id_personne';
        public $incrementing = true;
        protected $keyType = 'int';
        public $timestamps = false;

        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'email',
            'nom',
            'prenom',
            'mot_de_passe',
            'numero_telephone',
            'photo_profil',
        ];

        /**
         * The attributes that should be hidden for serialization.
         *
         * @var array<int, string>
         */
        protected $hidden = [
            'mot_de_passe',
            'remember_token',
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array<string, string>
         */
        protected $casts = [
            'email_verified_at' => 'datetime',
            // Retiré 'mot_de_passe' => 'hashed' car les mots de passe sont déjà hashés en base
        ];

        /**
         * Get the password for the user.
         */
        public function getAuthPassword()
        {
            return $this->mot_de_passe;
        }

        /**
         * Relations
         */
        public function admin()
        {
            return $this->hasOne(Admin::class, 'id_personne', 'id_personne');
        }

        public function gardien()
        {
            return $this->hasOne(Gardien::class, 'id_personne', 'id_personne');
        }

        public function resident()
        {
            return $this->hasOne(Resident::class, 'id_personne', 'id_personne');
        }

        public function groupes()
        {
            return $this->belongsToMany(GroupeMessage::class, 'personne_groupe', 'id_personne', 'id_groupe_message')
                        ->withPivot('date_adhesion', 'derniere_connexion');
        }

    public function messages()
    {
        return $this->hasMany(Message::class, 'id_auteur', 'id_personne');
    }

    public function invite()
    {
        return $this->hasOne(Invite::class, 'id_personne', 'id_personne');
    }

    /**
     * Déterminer le rôle de la personne
     */
    public function getRole()
    {
        if ($this->admin) {
            return 'admin';
        } elseif ($this->gardien) {
            return 'gardien';
        } elseif ($this->resident) {
            return 'resident';
        } elseif ($this->invite) {
            return 'invite';
        }
        return null;
    }

    /**
     * Vérifier si la personne est un invité
     */
    public function isGuest()
    {
        return $this->invite !== null && $this->invite->isActive();
    }

    /**
     * Vérifier si la personne a un mot de passe (n'est pas un invité)
     */
    public function hasPassword()
    {
        return !is_null($this->mot_de_passe);
    }

        /**
         * Vérifier si la personne a un rôle spécifique
         */
        public function hasRole($role)
        {
            return $this->getRole() === $role;
        }

        // Relations
        public function badges()
        {
            return $this->hasMany(Badge::class, 'id_utilisateur', 'id_personne');
        }

        // Accesseur pour le nom complet
        public function getNomCompletAttribute()
        {
            return $this->prenom . ' ' . $this->nom;
        }
    }
?>


