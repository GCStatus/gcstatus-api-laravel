<?php

namespace App\Formatters;

use Illuminate\Support\Carbon;
use App\Models\{MediaType, RequirementType};

class SteamAppDataFormatter
{
    /**
     * Format release date to a standard timestamp format.
     *
     * @param array<string, mixed> $releaseDate
     * @return string
     */
    public static function formatReleaseDate(array $releaseDate): string
    {
        /** @var string $dateString */
        $dateString = self::getArrayValue($releaseDate, 'date');

        if (!empty($releaseDate) && isset($releaseDate['date'])) {
            return (string)Carbon::createFromFormat('d M, Y', $dateString)?->toDateString();
        }

        return '';
    }

    /**
     * Format price to a standard price format.
     *
     * @param array<string, mixed> $details
     * @return int
     */
    public static function formatPrice(array $details): int
    {
        /** @var array<string, mixed> $overview */
        $overview = $details['price_overview'] ?? [];

        /** @var int $final */
        $final = self::getArrayValue($overview, 'final', 0);

        return $final;
    }

    /**
     * Format requirements to a standard requirements format.
     *
     * @param array<string, mixed> $details
     * @return list<array<string, array<string, string>|string>>
     */
    public static function formatRequirements(array $details): array
    {
        $requirementTypes = [
            'pc_requirements' => RequirementType::WINDOWS_OS_TYPE,
            'mac_requirements' => RequirementType::MAC_OS_TYPE,
            'linux_requirements' => RequirementType::LINUX_OS_TYPE,
        ];

        $requirements = [];

        foreach ($requirementTypes as $key => $osType) {
            /** @var array<string, mixed> $reqDetails */
            $reqDetails = self::getArrayValue($details, $key, []);

            if (!empty($reqDetails)) {
                foreach ($reqDetails as $level => $html) {
                    /** @var string $html */
                    if (!self::isEmptyRequirement($html)) {
                        $requirements[] = [
                            'os' => $osType,
                            'potential' => $level,
                            'attributes' => self::extractRequirements($html),
                        ];
                    }
                }
            }
        }

        return $requirements;
    }

    /**
     * Format the language to a standard language format.
     *
     * @param array<string, mixed> $details
     * @return list<array<string, bool|string>>
     */
    public static function formatLanguages(array $details): array
    {
        $formattedLanguages = [];

        /** @var string $languages */
        $languages = self::getArrayValue($details, 'supported_languages', '');

        if (!trim($languages)) {
            return $formattedLanguages;
        }

        $splitted = explode(', ', $languages);

        $audioSupportRegex = '/(.*?)<strong>\*<\/strong>?/i';

        foreach ($splitted as $entry) {
            /** @var string $entry */
            $entry = preg_replace('/<br>.*$/i', '', $entry);

            if (preg_match($audioSupportRegex, $entry, $matches)) {
                $formattedLanguages[] = [
                    'language' => trim($matches[1]),
                    'audio'    => true,
                ];
            } else {
                $formattedLanguages[] = [
                    'language' => $entry,
                    'audio'    => false,
                ];
            }
        }

        return $formattedLanguages;
    }

    /**
     * Format screenshots and movies to a standard gallery.
     *
     * @param array<string, mixed> $details
     * @return list<array<string, int|string>>
     */
    public static function formatGalleries(array $details): array
    {
        $galleries = [];

        /** @var array<string, mixed> $screenshots */
        $screenshots = self::getArrayValue($details, 'screenshots', []);

        foreach ($screenshots as $screenshot) {
            /** @var string $path */
            /** @var array<string, mixed> $screenshot */
            $path = self::getArrayValue($screenshot, 'path_full', '');

            if (!empty($path)) {
                $galleries[] = [
                    'type' => MediaType::PHOTO_CONST_ID,
                    'path' => $path,
                ];
            }
        }

        /** @var array<string, mixed> $movies */
        $movies = self::getArrayValue($details, 'movies', []);

        foreach ($movies as $movie) {
            /** @var string $path */
            /** @var array<string, mixed> $movie */
            $path = self::getArrayValue($movie, 'webm.max', '');

            if (!empty($path)) {
                $galleries[] = [
                    'type' => MediaType::VIDEO_CONST_ID,
                    'path' => $path,
                ];
            }
        }

        return $galleries;
    }

    /**
     * Format condition to a standard condition.
     *
     * @param array<string, mixed> $details
     * @return string
     */
    public static function formatCondition(array $details): string
    {
        /** @var array<string, mixed> $overview */
        $overview = $details['price_overview'] ?? [];

        /** @var int $discount */
        $discount = self::getArrayValue($overview, 'discount_percent', 0);

        if ($discount > 0) {
            return 'sale';
        }

        /** @var array<string, mixed> $release_date */
        $release_date = $details['release_date'] ?? [];

        /** @var bool $coming_soon */
        $coming_soon = self::getArrayValue($release_date, 'coming_soon', true);

        if (!$coming_soon) {
            /** @var array<string, mixed> $recommendations */
            $recommendations = $details['recommendations'] ?? [];

            /** @var int $total */
            $total = self::getArrayValue($recommendations, 'total', 0);

            if ($total > 500000) {
                return 'hot';
            }

            if ($total > 100000) {
                return 'popular';
            }
        }

        return 'common';
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
            'dx' => '/<strong>DirectX:<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
            't_os' => '/<strong>(?:OS|SO)\s*\*?\s*:<\/strong>\s*(.*?)\s*(?:<br|<\/li)/i',
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
     * Safely retrieve a nested value from an associative array using dot notation.
     *
     * @param array<string, mixed> $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private static function getArrayValue(array $array, string $key, mixed $default = null): mixed
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        $keys = explode('.', $key);

        foreach ($keys as $key) {
            if (!is_array($array) || !array_key_exists($key, $array)) {
                return $default;
            }

            $array = $array[$key];
        }

        return $array;
    }
}
