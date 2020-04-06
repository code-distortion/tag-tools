<?php

namespace CodeDistortion\TagTools\Internal;

interface JsSourceInterface
{
    /**
     * Generate a hash to detect multiple uses.
     *
     * @return string
     */
    public function duplicationHash(): string;

    /**
     * Retrieve the priority.
     *
     * @return integer
     */
    public function getPriority(): int;

    /**
     * Generate the output to use.
     *
     * @param string $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     */
    public function render(string $leadingWhitespace = ''): string;
}
