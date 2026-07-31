<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    /**
     * Indicates that the model does not have a created_at column.
     */
    const CREATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'store_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'store_name',
        'wa_numbers',
        'wa_template',
        'address',
        'social_links',
        'logo_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wa_numbers' => 'array',
            'social_links' => 'array',
        ];
    }

    /**
     * Get the singleton instance of store settings.
     */
    public static function instance(): ?static
    {
        return static::first();
    }
}
