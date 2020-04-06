<?php

namespace CodeDistortion\TagTools\Internal\Js;

use CodeDistortion\TagTools\Internal\JsSourceInterface;
use CodeDistortion\TagTools\Internal\Traits\HasIsFileCheckTrait;
use CodeDistortion\TagTools\Internal\Traits\HasPriorityTrait;
use Illuminate\Filesystem\Filesystem;

/**
 * Represent a source of javascript that will be embedded into the page.
 */
class EmbedJs implements JsSourceInterface
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
     * Either the path to a file or the javascript itself.
     *
     * @var string
     */
    protected $js = '';


    /**
     * Constructor.
     *
     * @param string     $js         Either the path to the js file, or a javascript string.
     * @param Filesystem $filesystem To access the filesystem.
     */
    public function __construct(string $js, Filesystem $filesystem)
    {
        $this->js = $js;
        $this->filesystem = $filesystem;
    }


    /**
     * Generate a hash to be used when detecting multiple uses.
     *
     * @return string
     */
    public function duplicationHash(): string
    {
        return __CLASS__.'-'.$this->js;
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
        $js = $this->isAFile($this->js, $this->filesystem)
            ? $this->filesystem->get($this->js)
            : $this->js;

        $return = $leadingWhitespace.'<script>'.PHP_EOL;
        $return .= rtrim($js, PHP_EOL).PHP_EOL;
        $return .= $leadingWhitespace.'</script>'.PHP_EOL;

        return $return;
    }
}
