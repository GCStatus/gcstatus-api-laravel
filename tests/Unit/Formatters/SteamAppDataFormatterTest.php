<?php

namespace Tests\Unit\Formatters;

use Tests\TestCase;
use App\Models\MediaType;
use Illuminate\Support\Carbon;
use App\Formatters\SteamAppDataFormatter;

class SteamAppDataFormatterTest extends TestCase
{
    /**
     * Test if can format the release date correctly.
     *
     * @return void
     */
    public function test_if_can_format_the_release_date_correctly(): void
    {
        // Valid
        $releaseDate = ['coming_soon' => false, 'date' => '20 Nov, 2000'];

        $this->assertEquals('2000-11-20', SteamAppDataFormatter::formatReleaseDate($releaseDate));

        // Coming soon
        $releaseDate = ['coming_soon' => true, 'date' => '20 Nov, 2000'];

        $this->assertEquals(Carbon::today()->addYear()->toDateString(), SteamAppDataFormatter::formatReleaseDate($releaseDate));

        // Invalid
        $releaseDate = [];

        $this->assertEquals(Carbon::today()->toDateString(), SteamAppDataFormatter::formatReleaseDate($releaseDate));
    }

    /**
     * Test if can format the price correctly.
     *
     * @return void
     */
    public function test_if_can_format_the_price_correctly(): void
    {
        // Valid
        $priceOverview = ['price_overview' => ['final' => 9999]];

        $this->assertEquals(9999, SteamAppDataFormatter::formatPrice($priceOverview));

        // Invalid
        $priceOverview = [];

        $this->assertEquals(0, SteamAppDataFormatter::formatPrice($priceOverview));
    }

    /**
     * Test if can format requirements correctly.
     *
     * @return void
     */
    public function test_if_can_format_requirements_correctly(): void
    {
        // Valid
        $requirements = [
            'pc_requirements' => [
                'minimum' => '<strong>Minimum:</strong><br><ul class=\'bb_ul\'><li>Requires a 64-bit processor and operating system<br></li><li><strong>OS:</strong> Windows 10 64-bit<br></li><li><strong>Processor:</strong> Intel i5-4670k or AMD Ryzen 3 1200<br></li><li><strong>Memory:</strong> 8 GB RAM<br></li><li><strong>Graphics:</strong> NVIDIA GTX 1060 (6GB) or AMD RX 5500 XT (8GB) or Intel Arc A750<br></li><li><strong>DirectX:</strong> Version 12<br></li><li><strong>Storage:</strong> 190 GB available space<br></li><li><strong>Additional Notes:</strong> Windows version 2004 2020-05-27 19041. 6GB GPU is required</li></ul>',
                'recommended' => '<strong>Recommended:</strong><br><ul class=\'bb_ul\'><li>Requires a 64-bit processor and operating system<br></li><li><strong>OS:</strong> Windows 10 64-bit<br></li><li><strong>Processor:</strong> Intel i5-8600 or AMD Ryzen 5 3600<br></li><li><strong>Memory:</strong> 16 GB RAM<br></li><li><strong>Graphics:</strong> NVIDIA RTX 2060 Super or AMD RX 5700 or Intel Arc A770<br></li><li><strong>DirectX:</strong> Version 12<br></li><li><strong>Storage:</strong> 190 GB available space<br></li><li><strong>Additional Notes:</strong> Windows version 2004 2020-05-27 19041. 6GB GPU is required</li></ul>'
            ],
        ];

        $this->assertEquals([
            [
                'os' => 'windows',
                'potential' => 'minimum',
                'attributes' => [
                    't_os' => 'Windows 10 64-bit',
                    'cpu' => 'Intel i5-4670k or AMD Ryzen 3 1200',
                    'ram' => '8 GB RAM',
                    'gpu' => 'NVIDIA GTX 1060 (6GB) or AMD RX 5500 XT (8GB) or Intel Arc A750',
                    'dx' => 'Version 12',
                    'storage' => '190 GB available space',
                    'obs' => 'Windows version 2004 2020-05-27 19041. 6GB GPU is required',
                ]
            ],
            [
                'os' => 'windows',
                'potential' => 'recommended',
                'attributes' => [
                    't_os' => 'Windows 10 64-bit',
                    'cpu' => 'Intel i5-8600 or AMD Ryzen 5 3600',
                    'ram' => '16 GB RAM',
                    'gpu' => 'NVIDIA RTX 2060 Super or AMD RX 5700 or Intel Arc A770',
                    'dx' => 'Version 12',
                    'storage' => '190 GB available space',
                    'obs' => 'Windows version 2004 2020-05-27 19041. 6GB GPU is required',
                ],
            ],
        ], SteamAppDataFormatter::formatRequirements($requirements));

        // Valid fallback obs
        $requirements = [
            'pc_requirements' => [
                'minimum' => '<strong>Minimum:</strong><br><ul class="bb_ul"><li>Fallback note<br></li></ul>',
            ],
        ];

        $this->assertEquals([
            [
                'os' => 'windows',
                'potential' => 'minimum',
                'attributes' => [
                    'obs' => 'Fallback note',
                ]
            ],
        ], SteamAppDataFormatter::formatRequirements($requirements));

        // Invalid
        $requirements = [
            'pc_requirements' => [],
        ];

        $this->assertEquals([], SteamAppDataFormatter::formatRequirements($requirements));
    }

    /**
     * Test if can format languages correctly.
     *
     * @return void
     */
    public function test_if_can_format_languages_correctly(): void
    {
        // Valid
        $languages = 'English<strong>*</strong>, French<strong>*</strong>, Italian<br><strong>*</strong>languages with full audio support';

        $data = ['supported_languages' => $languages];

        $this->assertEquals([
            [
                'language' => 'English',
                'audio' => true,
            ],
            [
                'language' => 'French',
                'audio' => true,
            ],
            [
                'language' => 'Italian',
                'audio' => false,
            ],
        ], SteamAppDataFormatter::formatLanguages($data));

        // Invalid
        $data = ['supported_languages' => ''];

        $this->assertEquals([], SteamAppDataFormatter::formatLanguages($data));

        // No key
        $data = [];

        $this->assertEquals([], SteamAppDataFormatter::formatLanguages($data));
    }

    /**
     * Test if can format galleries.
     *
     * @return void
     */
    public function test_if_can_format_galleries(): void
    {
        // Valid
        $data = [
            'screenshots' => [
                [
                    'id' => 0,
                    'path_thumbnail' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.600x338.jpg?t=1738256985',
                    'path_full' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.1920x1080.jpg?t=1738256985',
                ],
            ],
            'movies' => [
                [
                    'id' => 257054534,
                    'name' => 'Launch Trailer (US-EN)',
                    'thumbnail' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/257054534/movie.293x165.jpg?t=1726759092',
                    'webm' => [
                        '480' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie480_vp9.webm?t=1726759092',
                        'max' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max_vp9.webm?t=1726759092'
                    ],
                    'mp4' => [
                        '480' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie480.mp4?t=1726759092',
                        'max' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max.mp4?t=1726759092'
                    ],
                    'highlight' => true
                ],
            ],
        ];

        $this->assertEquals([
            [
                'type' => MediaType::PHOTO_CONST_ID,
                'path' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.1920x1080.jpg?t=1738256985',
            ],
            [
                'type' => MediaType::VIDEO_CONST_ID,
                'path' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max_vp9.webm?t=1726759092',
            ],
        ], SteamAppDataFormatter::formatGalleries($data));

        // Invalid
        $data = [
            'screenshots' => [
                [
                    'id' => 0,
                    'path_thumbnail' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.600x338.jpg?t=1738256985',
                    'invalid' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.1920x1080.jpg?t=1738256985',
                ],
            ],
            'movies' => [
                [
                    'id' => 257054534,
                    'name' => 'Launch Trailer (US-EN)',
                    'thumbnail' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/257054534/movie.293x165.jpg?t=1726759092',
                    'invalid' => [
                        '480' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie480_vp9.webm?t=1726759092',
                        'max' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max_vp9.webm?t=1726759092'
                    ],
                    'mp4' => [
                        '480' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie480.mp4?t=1726759092',
                        'max' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max.mp4?t=1726759092'
                    ],
                    'highlight' => true
                ],
            ],
        ];

        $this->assertEquals([], SteamAppDataFormatter::formatGalleries($data));

        // Empty
        $data = [];

        $this->assertEquals([], SteamAppDataFormatter::formatGalleries($data));
    }

    /**
     * Test if can format the condition.
     *
     * @return void
     */
    public function test_if_can_format_the_condition(): void
    {
        // Sale
        $data = [
            'price_overview' => [
                'discount_percent' => fake()->numberBetween(1, 100),
            ],
        ];

        $this->assertEquals('sale', SteamAppDataFormatter::formatCondition($data));

        // Unreleased
        $data = [
            'release_date' => [
                'coming_soon' => true,
            ],
        ];

        $this->assertEquals('unreleased', SteamAppDataFormatter::formatCondition($data));

        // Hot
        $data = [
            'release_date' => [
                'coming_soon' => false,
            ],
            'recommendations' => [
                'total' => 500001,
            ],
        ];

        $this->assertEquals('hot', SteamAppDataFormatter::formatCondition($data));

        // Popular
        $data = [
            'release_date' => [
                'coming_soon' => false,
            ],
            'recommendations' => [
                'total' => 100001,
            ],
        ];

        $this->assertEquals('popular', SteamAppDataFormatter::formatCondition($data));

        // Common
        $data = [];

        $this->assertEquals('common', SteamAppDataFormatter::formatCondition($data));
    }
}
