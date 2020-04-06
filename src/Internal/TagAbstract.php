<?php

namespace CodeDistortion\TagTools\Internal;

abstract class TagAbstract
{
    /**
     * The alias registered for this class in Laravel.
     *
     * @var string
     */
    protected static $alias = 'TagSomething';

    /**
     * The directive registered for use in blade.
     *
     * @var string
     */
    protected static $bladeDirective = 'tagsomething';

    /**
     * The stub to inject into the page - that will be replaced with the generated css.
     *
     * @var string
     */
    protected $templateStub;


    /**
     * Return the alias to register in Laravel.
     *
     * @return string
     */
    public static function getAlias(): string
    {
        return static::$alias;
    }

    /**
     * Return the directive to register for use in blade.
     *
     * @return string
     */
    public static function getBladeDirective(): string
    {
        return static::$bladeDirective;
    }

    /**
     * To be called in the header blade-template - let the caller specify where the output needs to be injected.
     *
     * @return string
     */
    public function generate()
    {
        return $this->templateStub;
    }

    /**
     * Look before the directive to work out how much whitespace to add.
     *
     * @param string  $content The content to look in to.
     * @param integer $pos     The position of the directive in the content.
     * @return string
     */
    protected function determineLeadingWhitespace(string $content, int $pos): string
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
