<?php

namespace CodeDistortion\TagTools\Internal\Css;

use CodeDistortion\TagTools\Internal\CssSourceInterface;
use CodeDistortion\TagTools\Internal\Traits\HasIsFileCheckTrait;
use CodeDistortion\TagTools\Internal\Traits\HasPriorityTrait;
use CodeDistortion\RelCss\RelevantCss;
use Illuminate\Filesystem\Filesystem;

/**
 * Represent a source of css that will be embedded into the page.
 */
class EmbedCss implements CssSourceInterface
{
    use HasPriorityTrait;
    use HasIsFileCheckTrait;


    /**
     * Used to access the filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Either the path to a file or the css-definitions themselves.
     *
     * @var string
     */
    protected $css = '';

    /**
     * Parse the css for further processing and formatting output?.
     *
     * @var boolean
     */
    protected $parseCss = false;

    /**
     * Minify in the output (only when $parseCss = true)?.
     *
     * @var boolean
     */
    protected $minify = false;

    /**
     * Remove unused css form the output (only when $parseCss = true)?.
     *
     * @var boolean
     */
    protected $removeUnused = false;


    /**
     * Constructor.
     *
     * @param string     $css        Either the path to the css file, or a css string.
     * @param Filesystem $filesystem To access the filesystem.
     */
    public function __construct(string $css, Filesystem $filesystem)
    {
        $this->css = $css;
        $this->filesystem = $filesystem;
    }


    /**
     * Set the flags to format the output.
     *
     * @return static
     */
    public function format(): self
    {
        $this->parseCss = true;
        $this->minify = false;
        return $this;
    }

    /**
     * Set the flags to minify the output.
     *
     * @return static
     */
    public function minify(): self
    {
        $this->parseCss = $this->minify = true;
        return $this;
    }

    /**
     * Return the minify flag.
     *
     * @return boolean
     */
    public function getMinify(): bool
    {
        return $this->minify;
    }

    /**
     * Set the flags to output the content raw (default).
     *
     * @return static
     */
    public function raw(): self
    {
        $this->parseCss = false;
        $this->minify = false;
        return $this;
    }

    /**
     * Set the flag so that unused css-definitions are removed.
     *
     * @param boolean $removeUnused Remove unused css-definitions?.
     * @return static
     */
    public function removeUnused(bool $removeUnused = true): self
    {
        if ($removeUnused) {
            $this->parseCss = true;
        }
        $this->removeUnused = $removeUnused;
        return $this;
    }

    /**
     * Return the removeUnused flag.
     *
     * @return boolean
     */
    public function getRemoveUnused(): bool
    {
        return $this->removeUnused;
    }


    /**
     * Generate a hash to be used when detecting multiple uses.
     *
     * @return string
     */
    public function duplicationHash(): string
    {
        return __CLASS__.'-'.md5($this->css);
    }

    /**
     * Build a hash to be used when grouping these EmbedCss objects based on their settings.
     *
     * @return string
     */
    public function getSettingsHash(): string
    {
        return ($this->removeUnused ? 'removeUnused' : 'dontRemoveUnused')
            .($this->minify ? '-minify' : '-noMinify');
    }


    /**
     * Find out if this object will render itself (if not it should be added to a RelevantCss for further processing).
     *
     * @return boolean
     */
    public function willSelfRender(): bool
    {
        return !$this->parseCss;
    }

    /**
     * Generate the output to use.
     *
     * @param string $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException Thrown when the file can't be loaded.
     */
    public function render(string $leadingWhitespace = ''): string
    {
        $css = $this->isAFile($this->css, $this->filesystem)
            ? $this->filesystem->get($this->css)
            : $this->css;

        $return = $leadingWhitespace.'<style>'.PHP_EOL;
        $return .= rtrim($css, "\r\n\t ").PHP_EOL;
        $return .= $leadingWhitespace.'</style>'.PHP_EOL;

        return $return;
    }


    /**
     * Add the necessary details to the given RelevantCss object.
     *
     * @param RelevantCss $relevantCss The RelevantCss object to add the css to.
     * @return void
     */
    public function addToRelevantCss(RelevantCss $relevantCss): void
    {
        if ($this->isAFile($this->css, $this->filesystem)) {
            $relevantCss->cssFile($this->css);
        } else {
            $relevantCss->cssDefinitions($this->css);
        }
    }
}
