<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessUnitUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The business unit that belongs to
     * @return BelongsTo
     */
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    /**
     * The user that belongs to
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
