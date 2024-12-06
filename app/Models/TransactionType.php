<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionType extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionTypeFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
    ];

    /**
     * The subtraction type id.
     *
     * @var int
     */
    public const SUBTRACTION_TYPE_ID = 1;

    /**
     * The addition type id.
     *
     * @var int
     */
    public const ADDITION_TYPE_ID = 2;

    /**
     * The subtraction type.
     *
     * @var string
     */
    public const SUBTRACTION_TYPE = 'subtraction';

    /**
     * The addition type.
     *
     * @var string
     */
    public const ADDITION_TYPE = 'addition';

    /**
     * Get all of the transactions for the TransactionType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
