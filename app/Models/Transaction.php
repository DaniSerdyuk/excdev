<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transaction
 *
 * @property int       $id
 * @property int       $user_id
 * @property int       $amount
 * @property string    $type
 * @property string    $description
 * @property \DateTime $created_at
 *
 * @property-read User $balance
 */
class Transaction extends Model
{
    const UPDATED_AT = null;

    /** @var string[] $fillable */
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'created_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute
     */
    protected function createdAt(): Attribute
    {
        return Attribute::get(
            fn($value) => Carbon::parse($value)->toDayDateTimeString()
        );
    }
}
