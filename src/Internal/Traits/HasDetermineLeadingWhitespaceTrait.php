<?php

namespace CodeDistortion\TagTools\Internal\Traits;

/**
 * Detect the leading whitespace to use.
 */
trait HasDetermineLeadingWhitespaceTrait
{
    /**
     * Look before the directive to work out how much whitespace to add.
     *
     * @param string  $content The content to look in to.
     * @param integer $pos     The position of the directive in the content.
     * @return string
     */
    private function determineLeadingWhitespace(string $content, int $pos): string
    {
        // look before the directive to work out how much whitespace to add
        $aBitBefore = mb_substr($content, max(0, $pos - 200), min(200, $pos));
        $pos2 = mb_strrpos($aBitBefore, PHP_EOL);
        $whitespaceLength = ($pos2 !== false
            ? mb_strlen($aBitBefore) - ($pos2 + mb_strlen(PHP_EOL))
            : mb_strlen($aBitBefore));
        return str_repeat(' ', $whitespaceLength);
    }
}
