<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Balance
 *
 * @property int       $id
 * @property int       $user_id
 * @property int       $amount
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 *
 * @property-read User $balance
 */
class Balance extends Model
{
    /** @var string[] $fillable */
    protected $fillable = [
        'user_id',
        'amount'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
