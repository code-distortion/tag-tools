<?php

namespace CodeDistortion\TagTools\Internal\Traits;

use Illuminate\Filesystem\Filesystem;

/**
 * Allow the class to check if the given string is the path to a file.
 */
trait HasIsFileCheckTrait
{
    /**
     * The characters to look for that cannot appear in a filename.
     *
     * @var string[]
     */
    protected static $invalidFilenameChars = ['<', '>', '{', '}', ':'];


    /**
     * Whether this represents a file or not.
     *
     * @var boolean|null
     */
    protected $isFile = null;


    /**
     * Check to see if this object represents a file.
     *
     * @param string     $value      The value to check to see if it's a file.
     * @param Filesystem $filesystem The filesystem to use to check if the file exists.
     * @return boolean
     */
    private function isAFile(string $value, Filesystem $filesystem): bool
    {
        if (is_null($this->isFile)) {

            // check for invalid characters first - it's cheaper
            foreach (static::$invalidFilenameChars as $char) {
                if (mb_strpos($value, $char) !== false) {
                    $this->isFile = false;
                    break;
                }
            }

            // check if the file exists
            if (is_null($this->isFile)) {
                $this->isFile = $filesystem->exists($value);
            }
        }
        return (bool) $this->isFile;
    }
}
