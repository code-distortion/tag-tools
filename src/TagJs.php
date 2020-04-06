<?php

namespace CodeDistortion\TagTools;

use CodeDistortion\TagTools\Internal\Js\AsyncJs;
use CodeDistortion\TagTools\Internal\Js\DeferJs;
use CodeDistortion\TagTools\Internal\Js\EmbedJs;
use CodeDistortion\TagTools\Internal\Js\LinkJs;
use CodeDistortion\TagTools\Internal\JsSourceInterface;
use CodeDistortion\TagTools\Internal\TagAbstract;
use CodeDistortion\TagTools\Internal\Traits\HasCanPrioritiseSourcesTrait;
use Illuminate\Filesystem\Filesystem;

/**
 * Manage the javascript sources that need to be added to the page.
 */
class TagJs extends TagAbstract
{
    use HasCanPrioritiseSourcesTrait;


    /**
     * Used to access the filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The sources to get the js from.
     *
     * @var JsSourceInterface[]
     */
    protected $sources = [];

    /**
     * The alias registered for this class in Laravel.
     *
     * @var string
     */
    protected static $alias = 'TagJs';

    /**
     * The directive registered for use in blade.
     *
     * @var string
     */
    protected static $bladeDirective = 'tagjs';


    /**
     * Constructor.
     *
     * @param Filesystem $filesystem Used to access the filesystem.
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->templateStub = '@TagJs-'.md5(uniqid((string) mt_rand(), true));
    }


    /**
     * Add a js url to link to.
     *
     * @param string $url The js file url.
     * @return LinkJs
     */
    public function link(string $url): LinkJs
    {
        $source = new LinkJs($url);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add a js url to link to.
     *
     * @param string $url The js file url.
     * @return DeferJs
     */
    public function defer(string $url): DeferJs
    {
        $source = new DeferJs($url);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add a js url to link to.
     *
     * @param string $url The js file url.
     * @return AsyncJs
     */
    public function async(string $url): AsyncJs
    {
        $source = new AsyncJs($url);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add css (either the path to a file or a javascript string) to embed.
     *
     * @param string $js Either the path to the js file, or a string of javascript.
     * @return EmbedJs
     */
    public function embed(string $js): EmbedJs
    {
        $source = new EmbedJs($js, $this->filesystem);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }


    /**
     * Replace the stubs with javascript.
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
        foreach ($this->prioritisedSources() as $source) {
            $return .= $source->render($leadingWhitespace);
        }
        return $return;
    }
}
