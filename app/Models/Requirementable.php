<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class Requirementable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'os',
        'dx',
        'cpu',
        'gpu',
        'ram',
        'rom',
        'obs',
        'network',
        'requirementable_id',
        'requirement_type_id',
        'requirementable_type',
    ];

    /**
     * Get the requirementable for the Requirementable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function requirementable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the requirementType that owns the Requirementable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requirementType(): BelongsTo
    {
        return $this->belongsTo(RequirementType::class);
    }
}
