<?php

namespace CodeDistortion\TagTools\Internal\Css;

use CodeDistortion\TagTools\Internal\Traits\HasIsFileCheckTrait;
use CodeDistortion\RelCss\RelevantCss;
use Illuminate\Filesystem\Filesystem;

/**
 * Represent a source of html that will used when looking for used css.
 */
class AdditionalHtml
{
    use HasIsFileCheckTrait;


    /**
     * Used to access the filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Either the path to the html file, or an html string.
     *
     * @var string
     */
    protected $html = '';

    /**
     * Whether this represents a file or not.
     *
     * @var boolean|null
     */
    protected $isFile = null;


    /**
     * Constructor.
     *
     * @param string     $html       Either the path to the html file, or an html string.
     * @param Filesystem $filesystem To access the filesystem.
     */
    public function __construct(string $html, Filesystem $filesystem)
    {
        $this->html = $html;
        $this->filesystem = $filesystem;
    }


    /**
     * Generate a hash to detect multiple uses.
     *
     * @return string
     */
    public function duplicationHash(): string
    {
        return __CLASS__.'-'.md5($this->html);
    }


    /**
     * Add the necessary details to the given RelevantCss object.
     *
     * @param RelevantCss $relevantCss The RelevantCss object to add the html to.
     * @return void
     */
    public function addToRelevantCss(RelevantCss $relevantCss): void
    {
        if ($this->isAFile($this->html, $this->filesystem)) {
            $relevantCss->fileNeedsCss($this->html);
        } else {
            $relevantCss->contentNeedsCss($this->html);
        }
    }
}
