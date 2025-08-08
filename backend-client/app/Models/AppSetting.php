<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class AppSetting extends Model
    {
        protected $table = 'app_settings';
        public $timestamps = false;
        protected $fillable = [
            'app_name',
            'logo_url',
            'company_name',
            'app_tagline',
            'primary_color',
            'secondary_color',
            'accent_color',
            'background_color',
            'welcome_title',
            'welcome_message',
            'updated_at'
        ];
    }
?>


