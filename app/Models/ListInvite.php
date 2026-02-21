<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ListInvite
 *
 * @property string $id
 * @property string $list_id
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Lists $list
 *
 * @package App\Models
 */
class ListInvite extends Model
{
    use HasUuids;

    protected $table = 'list_invites';


    protected $fillable = [
        'list_id',
        'email'
    ];

    /**
     * @return BelongsTo<Lists, $this>
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(Lists::class);
    }
}
