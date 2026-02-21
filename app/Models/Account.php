<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Account
 *
 * @property string $id
 * @property string $user_id
 * @property string $device
 * @property string|null $device_id
 * @property Carbon $last_login_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 *
 * @package App\Models
 */
class Account extends Model
{
    use HasUuids;

    protected $table = 'accounts';

    protected $casts = [
        'last_login_at' => 'datetime'
    ];

    protected $fillable = [
        'user_id',
        'device',
        'device_id',
        'last_login_at'
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
