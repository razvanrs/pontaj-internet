<?php
namespace App\Helpers;

use Illuminate\Support\Str;

class StrHelper {
    /**
     * Clean and format a name string with proper capitalization
     *
     * @param string|null $value
     * @return string
     */
    public static function formatName($value)
    {
        if (empty($value)) {
            return '';
        }

        $normalized = self::normalizeRomanianDiacritics($value);

        // Remove leading/trailing spaces, replace multiple spaces with a single space,
        // and capitalize first letter of each word
        return Str::of($normalized)
            ->trim()
            ->squish()
            ->title()  // This capitalizes first letter of each word
            ->toString();
    }

    /**
     * Clean and format a name string with proper capitalization
     *
     * @param string|null $value
     * @return string
     */
    public static function formatAddress($value)
    {
        if (empty($value)) {
            return '';
        }

        $normalized = self::normalizeRomanianDiacritics($value);

        // Remove leading/trailing spaces, replace multiple spaces with a single space,
        // and capitalize first letter of each word with proper formatting for addresses
        return Str::of($normalized)  // Change Str::of($value) to Str::of($normalized)
            ->trim()
            ->squish()
            ->title()  // This capitalizes first letter of each word
            ->replaceMatches('/,(?!\s)/', ', ')    // Add space after comma if not present
            ->replaceMatches('/\.(?!$)(?!\s)(?![A-Za-z])/', '. ')  // Add space after dot if not followed by letter
            ->toString();
    }

    /**
     * Create an email-friendly version of a name string
     * Only allows a-z, 0-9 and dot characters
     *
     * @param string $value
     * @return string
     */
    public static function formatForEmail($value)
    {
        if(empty($value)) {
            return '';
        }

        return Str::of($value)
            ->lower()                               // Convert to lowercase for email
            ->trim()                                // Remove leading/trailing spaces
            ->replace(' ', '')                      // Remove all spaces
            ->replace('-', '.')                     // Replace hyphens with dots
            ->ascii()                               // Convert diacritics to ASCII equivalents
            ->replaceMatches('/[^a-z0-9.]/', '')    // Remove any character that's not a-z, 0-9 or dot
            ->toString();                           // Convert to string
    }

    public static function normalizeText($text)
    {
        if (empty($text)) {
            return '';
        }

        $text = trim($text);

        // Convert to lowercase
        $text = Str::lower($text);

        return $text;
    }

    /**
     * Convert older Romanian diacritic characters to modern Unicode equivalents
     *
     * @param string $text
     * @return string
     */
    public static function normalizeRomanianDiacritics($text)
    {
        if (empty($text)) {
            return '';
        }

        // Map of old diacritics to modern Unicode Romanian diacritics
        $diacriticReplacements = [
            // Lowercase forms
            'ş' => 'ș',  // s with cedilla to s with comma below (U+0163 to U+0219)
            'ţ' => 'ț',  // t with cedilla to t with comma below (U+015F to U+021B)

            // Uppercase forms
            'Ş' => 'Ș',  // S with cedilla to S with comma below (U+015E to U+0218)
            'Ţ' => 'Ț',  // T with cedilla to T with comma below (U+0162 to U+021A)

            // Some applications use the wrong characters
            'ș' => 'ș',  // Ensure consistency if already correct
            'ț' => 'ț',  // Ensure consistency if already correct
            'Ș' => 'Ș',  // Ensure consistency if already correct
            'Ț' => 'Ț',  // Ensure consistency if already correct

            // Preserve other Romanian diacritics
            'ă' => 'ă',  // a with breve (U+0103)
            'â' => 'â',  // a with circumflex (U+00E2)
            'î' => 'î',  // i with circumflex (U+00EE)
            'Ă' => 'Ă',  // A with breve (U+0102)
            'Â' => 'Â',  // A with circumflex (U+00C2)
            'Î' => 'Î',  // I with circumflex (U+00CE)
        ];

        return str_replace(array_keys($diacriticReplacements), array_values($diacriticReplacements), $text);
    }
}
