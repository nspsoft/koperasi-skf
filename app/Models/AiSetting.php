<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'description'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::find($key);
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, ?string $description = null): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAll(): array
    {
        return self::pluck('value', 'key')->toArray();
    }

    /**
     * Get AI configuration for frontend
     */
    public static function getConfig(): array
    {
        return [
            'enabled' => self::get('ai_enabled', 'true') === 'true',
            'provider' => self::get('ai_provider', 'ollama'),
            'url' => self::get('ai_url', 'http://localhost:11434'),
            'model' => self::get('ai_model', 'llama2'),
            'apiKey' => self::get('ai_api_key', ''),
            'systemPrompt' => self::get('ai_system_prompt', 'Kamu adalah AI Assistant.'),
            'fonnte_token' => self::get('fonnte_token', ''),
            'wa_bot_enabled' => self::get('wa_bot_enabled', 'false') === 'true',
        ];
    }
}
