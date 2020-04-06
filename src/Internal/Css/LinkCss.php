<?php

namespace CodeDistortion\TagTools\Internal\Css;

use CodeDistortion\TagTools\Internal\Traits\HasPriorityTrait;
use CodeDistortion\TagTools\Internal\CssSourceInterface;

/**
 * Represent a source of css that will linked to.
 */
class LinkCss implements CssSourceInterface
{
    use HasPriorityTrait;


    /**
     * The url to the css file.
     *
     * @var string
     */
    protected $url;


    /**
     * Constructor.
     *
     * @param string $url The css file url.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }


    /**
     * Generate a hash to detect multiple uses.
     *
     * @return string
     */
    public function duplicationHash(): string
    {
        return __CLASS__.'-'.md5($this->url);
    }

    /**
     * Find out if this object will render itself (if not it should be added to a RelevantCss for further processing).
     *
     * @return boolean
     */
    public function willSelfRender(): bool
    {
        return true;
    }

    /**
     * Generate the output to use.
     *
     * @param string $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     */
    public function render(string $leadingWhitespace = ''): string
    {
        return $leadingWhitespace.'<link href="'.e($this->url).'" rel="stylesheet" />'.PHP_EOL;
    }
}
