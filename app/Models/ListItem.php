<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\ListItem\ListItemAttributesData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ListItem
 *
 * @property string $id
 * @property string $list_id
 * @property string $user_id
 * @property string|null $product_id
 * @property string|null $name
 * @property string|null $description
 * @property int $version
 * @property bool $is_completed
 * @property Carbon|null $completed_at
 * @property string|null $completed_user_id
 * @property ListItemAttributesData $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Lists $list
 * @property User $user
 * @property User|null $completedUser
 *
 * @package App\Models
 */
class ListItem extends Model
{
    use HasUuids;

    protected $table = 'list_items';

    protected $casts = [
        'completed_at' => 'datetime',
        'data' => ListItemAttributesData::class,
    ];

    protected $fillable = [
        'list_id',
        'user_id',
        'product_id',
        'name',
        'description',
        'version',
        'is_completed',
        'completed_at',
        'completed_user_id',
        'data'
    ];

    protected $with = ['user', 'completedUser'];

    protected $appends = ['user_name', 'user_avatar', 'completed_user_name', 'completed_user_avatar', 'attributes'];

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['data'] = collect($data['data'])->filter(function ($value) {
            return $value !== null;
        })->all();
        return $data;
    }

    public function getUserNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getUserAvatarAttribute(): ?string
    {
        return $this->user->avatar;
    }

    public function getCompletedUserNameAttribute(): ?string
    {
        return $this->completedUser?->name;
    }

    public function getCompletedUserAvatarAttribute(): ?string
    {
        return $this->completedUser?->avatar;
    }

    public function getAttributesAttribute(): ListItemAttributesData
    {
        return $this->data;
    }

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
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function completedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_user_id');
    }
}
