<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'component',
        'bannerable_id',
        'bannerable_type',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'bannerable',
    ];

    /**
     * The home header carousel banners.
     *
     * @var string
     */
    public const HOME_HEADER_CAROUSEL_BANNERS = 'home-header-carousel';

    /**
     * Get the bannerable for the Bannerable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function bannerable(): MorphTo
    {
        return $this->morphTo();
    }
}
