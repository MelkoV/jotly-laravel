<?php

declare(strict_types=1);

namespace App\Models;

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
 * @property bool $completed
 * @property Carbon|null $completed_at
 * @property string|null $completed_user_id
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Lists $list
 * @property User|null $user
 *
 * @package App\Models
 */
class ListItem extends Model
{
    use HasUuids;

    protected $table = 'list_items';

    protected $casts = [
        'completed_at' => 'datetime',
        'data' => 'json'
    ];

    protected $fillable = [
        'list_id',
        'user_id',
        'product_id',
        'name',
        'description',
        'version',
        'completed',
        'completed_at',
        'completed_user_id',
        'data'
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
        return $this->belongsTo(User::class, 'completed_user_id');
    }
}
