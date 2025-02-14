<?php

namespace Tests\Unit\DTO;

use Tests\TestCase;
use App\DTO\SteamAppDTO;
use App\Models\MediaType;
use InvalidArgumentException;

class SteamAppDTOTest extends TestCase
{
    /**
     * Test if can throw an exception for invalid data to transform on DTO.
     *
     * @return void
     */
    public function test_if_can_throw_an_exception_for_invalid_data_to_transform_on_DTO(): void
    {
        $data = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: type');

        SteamAppDTO::validateAndGet($data);
    }

    /**
     * Test if steam app dto get from valid data.
     *
     * @return void
     */
    public function test_steam_app_dto_get_from_valid_data()
    {
        $data = [
            'steam_appid' => 123,
            'type' => 'game',
            'name' => 'Half-Life',
            'required_age' => '0',
            'is_free' => false,
            'detailed_description' => 'A great game.',
            'about_the_game' => 'Best FPS of all time.',
            'short_description' => 'Classic FPS',
            'release_date' => ['date' => '19 Nov, 1998'],
            'background_raw' => 'https://example.com/cover.jpg',
            'categories' => [['id' => 1, 'description' => 'Single-player']],
            'genres' => [['id' => 1, 'description' => 'Shooter']],
            'developers' => ['Valve'],
            'publishers' => ['Valve'],
            'price_overview' => ['discount_percent' => 10, 'final' => 999],
            'legal_notice' => 'All rights reserved.',
            'website' => 'https://example.com',
            'support_info' => ['email' => 'support@example.com', 'url' => 'https://support.example.com'],
            'supported_languages' => 'English<strong>*</strong>, French<strong>*</strong>, Italian<br><strong>*</strong>languages with full audio support',
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

        $dto = SteamAppDTO::validateAndGet($data);

        $this->assertEquals(123, $dto->appId);
        $this->assertEquals('Half-Life', $dto->title);
        $this->assertEquals(false, $dto->free);
        $this->assertEquals('1998-11-19', $dto->release_date);
        $this->assertEquals('sale', $dto->condition);
        $this->assertEquals('sale', $dto->condition);
        $this->assertEquals([
            [
                'type' => MediaType::PHOTO_CONST_ID,
                'path' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.1920x1080.jpg?t=1738256985',
            ],
            [
                'type' => MediaType::VIDEO_CONST_ID,
                'path' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max_vp9.webm?t=1726759092',
            ],
        ], $dto->galleries);
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
        ], $dto->languages);
    }
}
