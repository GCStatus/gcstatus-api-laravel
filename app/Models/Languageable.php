<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Languageable extends Model
{
    /** @use HasFactory<\Database\Factories\LanguageableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'language_id',
        'languageable_id',
        'languageable_type',
    ];

    /**
     * Get the languageable for the Languageable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function languageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the language that owns the Languageable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Language, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
