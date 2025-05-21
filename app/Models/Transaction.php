<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'date_transaction',
        'amount',
        'note',
        'image',
    ];

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeIncome($query)
    {
        return $query->whereHas('category', fn ($q) => $q->where('is_expense', false))
            ->where('user_id', auth()->id());
    }

    public function scopeExpense($query)
    {
        return $query->whereHas('category', fn ($q) => $q->where('is_expense', true))
            ->where('user_id', auth()->id());
    }
}
