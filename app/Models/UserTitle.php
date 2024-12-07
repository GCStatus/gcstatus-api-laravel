<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{Pivot, BelongsTo};

class UserTitle extends Pivot
{
    /** @use HasFactory<\Database\Factories\UserTitleFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'enabled',
        'user_id',
        'title_id',
    ];

    /**
     * Determine the incrementing for the pivot table.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'enabled' => 'bool',
        ];
    }

    /**
     * Get the user that owns the UserTitle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the title that owns the UserTitle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Title, $this>
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }
}
