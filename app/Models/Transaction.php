<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(TransactionObserver::class)]
class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'amount',
        'user_id',
        'description',
        'transaction_type_id',
    ];

    /**
     * The default relations to be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'type',
    ];

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TransactionType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }
}
