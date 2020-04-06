<?php

namespace CodeDistortion\TagTools\Internal\Traits;

/**
 * Lets the object have a priority.
 */
trait HasPriorityTrait
{
    /**
     * The priority that will be used.
     *
     * @var integer
     */
    protected $priority = 10;


    /**
     * Let the caller set the priority (lower has more priority).
     *
     * @param integer $priority The new priority to set.
     * @return static
     */
    public function priority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Retrieve the priority.
     *
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
