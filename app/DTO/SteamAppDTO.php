<?php

namespace App\DTO;

use App\Formatters\SteamAppDataFormatter;
use App\Contracts\Services\Validation\SteamAppValidatorInterface;

/**
 * Data Transfer Object for Steam Application Data.
 */
class SteamAppDTO
{
    /**
     * Create a new dto instance.
     *
     * @param int $appId
     * @param string $type
     * @param string $title
     * @param int $steamAppId
     * @param string $age
     * @param bool $free
     * @param array<int, int> $dlc
     * @param string $description
     * @param string $about
     * @param string $short_description
     * @param string $release_date
     * @param string $cover
     * @param array<int, mixed> $categories
     * @param array<int, mixed> $genres
     * @param array<int, string> $developers
     * @param array<int, string> $publishers
     * @param int $price
     * @param string|null $legal
     * @param list<array<string, array<string, string>|string>> $requirements
     * @param list<array<string, bool|string>> $languages
     * @param string|null $website
     * @param list<array<string, int|string>> $galleries
     * @param string $condition
     * @param array<string, string> $support
     */
    public function __construct(
        public int $appId,
        public string $type,
        public string $title,
        public int $steamAppId,
        public string $age,
        public bool $free,
        public array $dlc,
        public string $description,
        public string $about,
        public string $short_description,
        public string $release_date,
        public string $cover,
        public array $categories,
        public array $genres,
        public array $developers,
        public array $publishers,
        public int $price,
        public ?string $legal,
        public array $requirements,
        public array $languages,
        public ?string $website,
        public array $galleries,
        public string $condition,
        public array $support,
    ) {
    }

    /**
     * Factory method to create a DTO from a validated Steam API response.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    private static function fromArray(array $data): self
    {
        /** @var int $appId */
        $appId = $data['steam_appid'] ?? 0;

        /** @var string $type */
        $type = $data['type'] ?? '';

        /** @var string $title */
        $title = $data['name'];

        /** @var string $age */
        $age = $data['required_age'] ?? '0';

        /** @var string $description */
        $description = $data['detailed_description'] ?? '';

        /** @var string $about */
        $about = $data['about_the_game'] ?? '';

        /** @var string $short_description */
        $short_description = $data['short_description'] ?? '';

        /** @var string $cover */
        $cover = $data['background_raw'] ?? 'https://placehold.co/600x400/EEE/31343C';

        /** @var ?string $legal */
        $legal = $data['legal_notice'] ?? null;

        /** @var ?string $website */
        $website = $data['website'] ?? null;

        /** @var array<int, int> */
        $dlc = $data['dlc'] ?? [];

        /** @var array<string, mixed> $release_date */
        $release_date = $data['release_date'] ?? [];

        /** @var array<int, mixed> $categories */
        $categories = $data['categories'] ?? [];

        /** @var array<int, mixed> $genres */
        $genres = $data['genres'] ?? [];

        /** @var array<int, string> $developers */
        $developers = $data['developers'] ?? [];

        /** @var array<int, string> $publishers */
        $publishers = $data['publishers'] ?? [];

        /** @var array<string, string> $support */
        $support = $data['support_info'] ?? [];

        return new self(
            appId: $appId,
            type: $type,
            title: $title,
            steamAppId: $appId,
            age: $age,
            free: isset($data['is_free']) ? (bool)$data['is_free'] : false,
            dlc: $dlc,
            description: $description,
            about: $about,
            short_description: $short_description,
            release_date: SteamAppDataFormatter::formatReleaseDate($release_date),
            cover: $cover,
            categories: $categories,
            genres: $genres,
            developers: $developers,
            publishers: $publishers,
            price: SteamAppDataFormatter::formatPrice($data),
            legal: $legal,
            requirements: SteamAppDataFormatter::formatRequirements($data),
            languages: SteamAppDataFormatter::formatLanguages($data),
            website: $website,
            galleries: SteamAppDataFormatter::formatGalleries($data),
            condition: SteamAppDataFormatter::formatCondition($data),
            support: $support,
        );
    }

    /**
     * Validate Steam API response before creating DTO.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function validateAndGet(array $data): self
    {
        app(SteamAppValidatorInterface::class)->validate($data);

        return self::fromArray($data);
    }
}
