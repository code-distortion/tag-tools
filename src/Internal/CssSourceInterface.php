<?php

namespace CodeDistortion\TagTools\Internal;

interface CssSourceInterface
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
     * Find out if this object will render itself (if not it should be added to a RelevantCss for further processing).
     *
     * @return boolean
     */
    public function willSelfRender(): bool;

    /**
     * Generate the output to use.
     *
     * @param string $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     */
    public function render(string $leadingWhitespace = ''): string;
}
