<?php

namespace App\Models;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequirementType extends Model
{
    /** @use HasFactory<\Database\Factories\RequirementTypeFactory> */
    use HasFactory;
    use SoftDeletes;
    use CacheQueryBuilder;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'os',
        'potential',
    ];

    /**
     * The minimum potential type.
     *
     * @var string
     */
    public const MINIMUM_POTENTIAL_TYPE = 'minimum';

    /**
     * The recommended potential type.
     *
     * @var string
     */
    public const RECOMMENDED_POTENTIAL_TYPE = 'recommended';

    /**
     * The maximum potential type.
     *
     * @var string
     */
    public const MAXIMUM_POTENTIAL_TYPE = 'maximum';

    /**
     * The windows os type.
     *
     * @var string
     */
    public const WINDOWS_OS_TYPE = 'windows';

    /**
     * The linux os type.
     *
     * @var string
     */
    public const LINUX_OS_TYPE = 'linux';

    /**
     * The mac os type.
     *
     * @var string
     */
    public const MAC_OS_TYPE = 'mac';

    /**
     * Get all of the requirementables for the RequirementType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Requirementable, $this>
     */
    public function requirementables(): HasMany
    {
        return $this->hasMany(Requirementable::class);
    }
}
