<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ListAccess;
use App\Enums\ListType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Class Lists
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $short_url
 * @property string $type
 * @property string $owner_id
 * @property string $owner_name
 * @property string $owner_avatar
 * @property int $access
 * @property bool $is_template
 * @property bool $can_edit
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon $touched_at
 *
 * @property User $user
 * @property Collection|User[] $users
 * @property Collection|ListInvite[] $listInvites
 * @property Collection|ListItem[] $listItems
 *
 * @package App\Models
 */
class Lists extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $table = 'lists';

    protected $casts = [
        'access' => 'int',
        'touched_at' => 'datetime',
        'type' => ListType::class,
    ];

    protected $fillable = [
        'name',
        'description',
        'short_url',
        'type',
        'is_template',
        'owner_id',
        'access',
        'touched_at'
    ];

    protected $with = ['user'];

    protected $appends = ['owner_name', 'owner_avatar', 'can_edit'];

    public function getOwnerNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getOwnerAvatarAttribute(): ?string
    {
        return $this->user->avatar;
    }

    public function getCanEditAttribute(): bool
    {
        return Auth::id() &&
            (((string)Auth::id() === $this->owner_id || ($this->access & ListAccess::CanEdit->value) > 0));
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'list_users')
                    ->withPivot('id')
                    ->withTimestamps();
    }

    /**
     * @return HasMany<ListInvite, $this>
     */
    public function listInvites(): HasMany
    {
        return $this->hasMany(ListInvite::class);
    }

    /**
     * @return HasMany<ListItem, $this>
     */
    public function listItems(): HasMany
    {
        return $this->hasMany(ListItem::class);
    }
}
