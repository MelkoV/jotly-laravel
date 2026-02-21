<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ListUser
 *
 * @property string $id
 * @property string $list_id
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Lists $list
 * @property User $user
 *
 * @package App\Models
 */
class ListUser extends Model
{
    use HasUuids;

    protected $table = 'list_users';

    protected $fillable = [
        'list_id',
        'user_id'
    ];

    /**
     * @return BelongsTo<Lists, $this>
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(Lists::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
