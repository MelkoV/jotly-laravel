<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product
 *
 * @property string $id
 * @property string $name
 * @property string $category_id
 * @property string $unit
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property ProductCategory $productCategory
 *
 * @package App\Models
 */
class Product extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'category_id',
        'unit'
    ];

    /**
     * @return BelongsTo<ProductCategory, $this>
     */
    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
