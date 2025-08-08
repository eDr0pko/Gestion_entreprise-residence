<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory;

    protected $table = 'app_settings';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'category'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Récupérer une valeur de paramètre par clé
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Définir ou mettre à jour une valeur de paramètre
     */
    public static function set($key, $value, $type = 'string', $description = '', $category = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'category' => $category
            ]
        );
    }

    /**
     * Récupérer tous les paramètres par catégorie
     */
    public static function getByCategory($category = null)
    {
        $query = self::query();
        
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->get()->keyBy('key');
    }

    /**
     * Récupérer tous les paramètres formatés pour l'API
     */
    public static function getAllFormatted()
    {
        return self::all()->groupBy('category')->map(function ($items) {
            return $items->keyBy('key')->map(function ($item) {
                return [
                    'value' => $item->value,
                    'type' => $item->type,
                    'description' => $item->description
                ];
            });
        });
    }
}
