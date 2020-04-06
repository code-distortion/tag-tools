<?php

namespace CodeDistortion\TagTools;

use CodeDistortion\TagTools\Internal\TagAbstract;

/**
 * Manage the favicon sources that need to be added to the page.
 */
class TagFav extends TagAbstract
{
    /**
     * @avr string[]
     */
    protected $favs = [];

    /**
     * The alias registered for this class in Laravel.
     *
     * @var string
     */
    protected static $alias = 'TagFav';

    /**b
     * The directive registered for use in blade.
     *
     * @var string
     */
    protected static $bladeDirective = 'tagfav';


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->templateStub = '@TagFav-'.md5(uniqid((string) mt_rand(), true));
    }


    /**
     * Add a fav.
     *
     * @param string $fav The fav to add.
     * @return void
     */
    public function add(string $fav): void
    {
        $this->favs[$fav] = $fav;
    }


    /**
     * Replace the stubs with favs.
     *
     * @param string $html The content to process.
     * @return string
     */
    public function replaceStubs(
        string $html
    ): string {

        $pos = mb_strpos($html, $this->templateStub);
        if ($pos === false) {
            return $html;
        }

        $count = 20;
        do {
            // render the replacement javascript
            $leadingWhitespace = $this->determineLeadingWhitespace($html, $pos);
            $replacement = $this->render($leadingWhitespace);

            // remove the leading whitespace from the first line
            $replacement = mb_substr($replacement, mb_strlen($leadingWhitespace));

            // replace the first stub
            $html = substr_replace($html, $replacement, $pos, strlen($this->templateStub));

            // look for the next
            $pos = mb_strpos($html, $this->templateStub);
        } while (($pos !== false) && ($count-- > 0));

        return $html;
    }

    /**
     * Generate the output to be injected.
     *
     * @param string $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     */
    public function render(string $leadingWhitespace = ''): string
    {
        $return = '';
        foreach ($this->favs as $fav) {
            $return .= $leadingWhitespace.$fav.PHP_EOL;
        }
        return $return;
    }
}
