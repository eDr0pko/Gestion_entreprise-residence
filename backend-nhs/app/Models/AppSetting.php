<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Cache;

    class AppSetting extends Model
    {
        use HasFactory;

        protected $table = 'app_settings';
        protected $primaryKey = 'id';
        public $timestamps = false;

        protected $fillable = [
            'setting_key',
            'setting_value',
            'setting_type',
            'description',
            'updated_by'
        ];

        protected $casts = [
            'updated_at' => 'datetime',
        ];

        /**
         * Relation avec le modèle Personne (utilisateur qui a mis à jour)
         */
        public function updatedBy()
        {
            return $this->belongsTo(Personne::class, 'updated_by', 'id_personne');
        }

        /**
         * Récupérer une valeur de paramètre avec cache
         */
        public static function get($key, $default = null)
        {
            return Cache::remember("app_setting_{$key}", 3600, function () use ($key, $default) {
                $setting = self::where('setting_key', $key)->first();
                return $setting ? $setting->setting_value : $default;
            });
        }

        /**
         * Définir une valeur de paramètre et vider le cache
         */
        public static function set($key, $value, $type = 'text', $updatedBy = null)
        {
            $setting = self::updateOrCreate(
                ['setting_key' => $key],
                [
                    'setting_value' => $value,
                    'setting_type' => $type,
                    'updated_by' => $updatedBy
                ]
            );

            Cache::forget("app_setting_{$key}");
            Cache::forget('app_settings_all');

            return $setting;
        }

        /**
         * Récupérer tous les paramètres avec cache
         */
        public static function getAll()
        {
            return Cache::remember('app_settings_all', 3600, function () {
                return self::all()->pluck('setting_value', 'setting_key')->toArray();
            });
        }

        /**
         * Vider tout le cache des paramètres
         */
        public static function clearCache()
        {
            $settings = self::all();
            foreach ($settings as $setting) {
                Cache::forget("app_setting_{$setting->setting_key}");
            }
            Cache::forget('app_settings_all');
        }

        /**
         * Récupérer la valeur formatée selon le type
         */
        public function getFormattedValueAttribute()
        {
            switch ($this->setting_type) {
                case 'boolean':
                    return filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN);
                case 'json':
                    return json_decode($this->setting_value, true);
                default:
                    return $this->setting_value;
            }
        }

        /**
         * Définir la valeur formatée selon le type
         */
        public function setFormattedValue($value)
        {
            switch ($this->setting_type) {
                case 'boolean':
                    $this->setting_value = $value ? 'true' : 'false';
                    break;
                case 'json':
                    $this->setting_value = json_encode($value);
                    break;
                default:
                    $this->setting_value = $value;
            }
        }
    }
?>


