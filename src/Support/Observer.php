<?php namespace Veemo\Core\Support;

use Veemo\Core\Traits\FiresCallbacks;
use Illuminate\Bus\Dispatcher as CommandDispatcher;
use Illuminate\Events\Dispatcher as EventDispatcher;

/**
 * Class Observer
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Support
 */
class Observer
{

    use FiresCallbacks;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    protected $events;

    /**
     * The command dispatcher.
     *
     * @var CommandDispatcher
     */
    protected $commands;

    /**
     * Create a new EloquentObserver instance.
     *
     * @param EventDispatcher   $events
     * @param CommandDispatcher $commands
     */
    public function __construct(EventDispatcher $events, CommandDispatcher $commands)
    {
        $this->events   = $events;
        $this->commands = $commands;
    }
}
