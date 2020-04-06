<?php

namespace CodeDistortion\TagTools\Internal\Traits;

/**
 * Lets the object prioritise its sources.
 */
trait HasCanPrioritiseSourcesTrait
{
    /**
     * Put the sources in order of priority.
     *
     * @return array
     */
    private function prioritisedSources(): array
    {
        // put the sources into ordered priority groups
        $sourceGroups = [];
        foreach ($this->sources as $source) {
            $sourceGroups[$source->getPriority()][] = $source;
        }
        ksort($sourceGroups);

        // flatten the sources
        $sources = [];
        foreach (array_keys($sourceGroups) as $priority) {
            foreach ($sourceGroups[$priority] as $source) {
                $sources[] = $source;
            }
        }
        return $sources;
    }
}
