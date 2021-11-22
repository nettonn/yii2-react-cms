<?php namespace app\components\validators;

/**
 * Validate helper.
 *
 * @author Maksim T. <zapalm@yandex.com>
 */
class PunycodeValidateHelper
{
    /**
     * Checks if the given domain is in Punycode.
     *
     * @param string $domain The domain to check.
     *
     * @return bool Whether the domain is in Punycode.
     *
     * @see https://developer.mozilla.org/en-US/docs/Mozilla/Internationalized_domain_names_support_in_Mozilla#ASCII-compatible_encoding_.28ACE.29
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
    public static function isPunycodeDomain($domain)
    {
        $hasPunycode = false;

        foreach (explode('.', $domain) as $part) {
            if (false === static::isAscii($part)) {
                return false;
            }

            if (static::isPunycode($part)) {
                $hasPunycode = true;
            }
        }

        return $hasPunycode;
    }

    /**
     * Checks if the given value is in ASCII character encoding.
     *
     * @param string $value The value to check.
     *
     * @return bool Whether the value is in ASCII character encoding.
     *
     * @see https://en.wikipedia.org/wiki/ASCII
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
    public static function isAscii($value)
    {
        return ('ASCII' === mb_detect_encoding($value, 'ASCII', true));
    }

    /**
     * Checks if the given value is in Punycode.
     *
     * @param string $value The value to check.
     *
     * @return bool Whether the value is in Punycode.
     *
     * @throws \LogicException If the string is not encoded by UTF-8.
     *
     * @see https://en.wikipedia.org/wiki/Punycode
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
    public static function isPunycode($value)
    {
        if (false === static::isAscii($value)) {
            return false;
        }

        if ('UTF-8' !== mb_detect_encoding($value, 'UTF-8', true)) {
            throw new \LogicException('The string should be encoded by UTF-8 to do the right check.');
        }

        return (0 === mb_stripos($value, 'xn--', 0, 'UTF-8'));
    }
}
