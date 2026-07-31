<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;

    /**
     * Indicates that the model does not have an updated_at column.
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'items_json',
        'buyer_info_json',
        'total_amount',
        'wa_number_used',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'items_json' => 'array',
            'buyer_info_json' => 'array',
            'total_amount' => 'decimal:2',
        ];
    }
}
