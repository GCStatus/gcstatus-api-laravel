<?php

namespace App\DTO;

use App\Models\MediaType;
use Exception;
use Illuminate\Support\Carbon;

/**
 * Data Transfer Object for Steam Application Data.
 *
 * Represents the structure of the data received from the Steam API.
 *
 * @package App\DTO
 */
class SteamAppDTO
{
    /**
     * Create a new class instance.
     *
     * @param string $type
     * @param string $title
     * @param int $steamAppId
     * @param string $age
     * @param bool $free
     * @param array $dlc
     * @param string $description
     * @param string $about
     * @param string $short_description
     * @param ?string $release_date
     * @param string $cover
     * @param array $categories
     * @param array $genres
     * @param array $developers
     * @param array $publishers
     * @param ?int $price
     * @param ?string $legal
     * @param array<string, mixed> $requirements
     * @param array<string, mixed> $languages
     * @param ?string $website
     * @param string $condition
     * @return void
     */
    public function __construct(
        public string  $type,
        public string  $title,
        public int     $steamAppId,
        public string  $age,
        public bool    $free,
        public array   $dlc,
        public string  $description,
        public string  $about,
        public string  $short_description,
        public string  $release_date,
        public string  $cover,
        public array   $categories,
        public array   $genres,
        public array   $developers,
        public array   $publishers,
        public ?int    $price,
        public ?string $legal,
        public array   $requirements,
        public array   $languages,
        public ?string $website,
        public array   $galleries,
        public string  $condition,
    ) {
    }

    /**
     * Factory method to create a DTO from a raw Steam API response array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            title: $data['name'],
            steamAppId: $data['steam_appid'] ?? 0,
            age: $data['required_age'],
            free: $data['is_free'] ?? false,
            dlc: $data['dlc'] ?? [],
            description: $data['detailed_description'],
            about: $data['about_the_game'],
            short_description: $data['short_description'],
            release_date: self::formatReleaseDate($data['release_date'] ?? []),
            cover: $data['background_raw'],
            categories: $data['categories'] ?? [],
            genres: $data['genres'] ?? [],
            developers: $data['developers'] ?? [],
            publishers: $data['publishers'] ?? [],
            price: self::formatPrice($data['price_overview'] ?? []),
            legal: $data['legal_notice'],
            requirements: self::formatRequirements($data) ?? [],
            languages: self::formatLanguages($data['supported_languages']) ?? [],
            website: $data['website'],
            galleries: self::formatGalleries($data['screenshots'], $data['movies']) ?? [],
            condition: self::formatCondition($data),
        );
    }

    /**
     * Format release date to a standard timestamp format.
     *
     * @param array<string, mixed> $releaseDate
     * @return ?string
     */
    private static function formatReleaseDate(array $releaseDate): ?string
    {
        if (!empty($releaseDate) && isset($releaseDate['date'])) {
            try {
                return Carbon::createFromFormat('M d, Y', $releaseDate['date'])->toDateString();
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Format price to a standard price format.
     *
     * @param array<string, mixed> $priceOverview
     * @return ?int
     */
    private static function formatPrice(array $priceOverview): ?int
    {
        if (!empty($priceOverview) && isset($priceOverview['final'])) {
            return $priceOverview['final'];
        }

        return null;
    }

    /**
     * Format requirements to a standard requirements format.
     *
     * @param array<string, mixed> $details
     * @return array<string, mixed>
     */
    public static function formatRequirements(array $details): array
    {
        $requirementTypes = [
            'pc_requirements' => 'windows',
            'mac_requirements' => 'mac',
            'linux_requirements' => 'linux',
        ];

        $results = [];

        foreach ($requirementTypes as $reqType => $osType) {
            if (!isset($details[$reqType])) {
                continue;
            }

            foreach ($details[$reqType] as $level => $html) {
                if (self::isEmptyRequirement($html)) {
                    continue;
                }

                $parsed = self::extractRequirements($html);

                $results[$osType][$level] = $parsed;
            }
        }

        return $results;
    }

    /**
     * Check requirement is empty.
     *
     * @param string $html
     * @return bool
     */
    private static function isEmptyRequirement(string $html): bool
    {
        return preg_match('/<ul class=\"bb_ul\"><\/ul>/', $html) === 1;
    }

    /**
     * Extract the requirements from string.
     *
     * @param string $html
     * @return array<string, string>
     */
    private static function extractRequirements(string $html): array
    {
        $patterns = [
            'os' => '/<strong>(?:OS|SO)\s*:<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
            'dx' => '/<strong>DirectX:<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
            'cpu' => '/<strong>(?:Processor|Processador):<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
            'ram' => '/<strong>Mem(?:ory|\xF3ria):<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
            'gpu' => '/<strong>(?:Graphics|Placa de v[ií]deo):<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
            'storage' => '/<strong>(?:Storage|Armazenamento):<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
        ];

        $obsPatternNotes = '/<strong>(?:Additional Notes|Outras observa[cç][õo]es)(?:.*?)<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i';
        $obsPatternFallback = '/<strong>(?:Mínimos|Recomendados|Minimum|Recommended):<\/strong><br><ul class="bb_ul"><li>(.*?)<br><\/li>/i';

        $results = [];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $results[$key] = trim($matches[1]);
            }
        }

        if (preg_match($obsPatternNotes, $html, $matches) && trim($matches[1]) !== '') {
            $results['obs'] = trim($matches[1]);
        } elseif (preg_match($obsPatternFallback, $html, $matches)) {
            $results['obs'] = strip_tags(trim($matches[1]));
        }

        return $results;
    }

    /**
     * Format the language to a standard language format.
     *
     * @param string $languages
     * @return array<string, mixed>
     */
    private static function formatLanguages(string $languages): array
    {
        $splitted = explode(', ', $languages);

        $audioSupportRegex = '/(.*?)<strong>\*<\/strong>?/i';

        $formattedLanguages = [];

        foreach ($splitted as $key => $entry) {
            $entry = preg_replace('/<br>.*$/i', '', $entry);

            $hasAudio = false;

            if (preg_match($audioSupportRegex, $entry, $matches)) {
                $langName = trim($matches[1]);
                $hasAudio = true;
            } else {
                $langName = $entry;
                $hasAudio = false;
            }

            $formattedLanguages[$key] = [
                'language' => $langName,
                'audio' => $hasAudio,
            ];
        }

        return $formattedLanguages;
    }

    /**
     * Format screenshots and movies to a standard gallery.
     *
     * @param array<string, mixed> $screenshots
     * @param array<string, mixed> $movies
     * @return array<string, mixed>
     */
    private static function formatGalleries(array $screenshots, array $movies): array
    {
        $count = 0;
        $galleries = [];

        foreach ($screenshots as $screenshot) {
            $galleries[$count] = [
                'type' => MediaType::PHOTO_CONST_ID,
                'path' => $screenshot['path_full'] ?? '',
            ];

            $count++;
        }

        foreach ($movies as $movie) {
            $galleries[$count] = [
                'type' => MediaType::VIDEO_CONST_ID,
                'path' => $movie['webm']['max'] ?? '',
            ];

            $count++;
        }

        return $galleries;
    }

    /**
     * Format condition to a standard condition.
     *
     * @param array<string, mixed> $details
     * @return string
     */
    private static function formatCondition(array $details): string
    {
        if (isset($details['price_overview']) && $details['price_overview']['discount_percent'] > 0) {
            return 'sale';
        }

        if (isset($details['recommendations']) && !$details['release_date']['coming_soon']) {
            $recommendations = $details['recommendations']['total'];

            if ($recommendations > 100000) {
                return 'popular';
            } elseif ($recommendations > 500000) {
                return 'hot';
            }
        }

        return 'commom';
    }
}
