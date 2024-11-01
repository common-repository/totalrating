<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Emitter;
use TotalRatingVendors\TotalSuite\Foundation\Event;

/**
 * Class ActionEmitter
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
class ActionEmitter extends Emitter
{
    /**
     * High priority
     */
    const P_HIGH = 0;

    /**
     * Normal priority
     */
    const P_NORMAL = 10;

    /**
     * Low priority
     */
    const P_LOW = 100;

    /**
     * @var array
     */
    protected $actionHandlersListeners = [];

    /**
     * @param string $event
     * @param callable|\TotalRatingVendors\League\Event\ListenerInterface $listener
     * @param int $priority
     * @return ActionEmitter
     */
    public function addListener($event, $listener, $priority = self::P_NORMAL)
    {
        if (is_string($listener) &&
            is_a($event, Event::class, true) &&
            is_a($listener, ActionHandler::class, true)) {

            $this->actionHandlersListeners[$listener] = $this->actionCallback($event, $listener);

            add_action(
                $event::alias(),
                $this->actionHandlersListeners[$listener],
                $priority,
                PHP_INT_MAX
            );

            $listener = $this->actionHandlersListeners[$listener];
        }

        return parent::addListener($event, $listener, $priority);
    }

    /**
     * @param string $event
     * @param callable|\TotalRatingVendors\League\Event\ListenerInterface $listener
     * @return ActionEmitter
     */
    public function removeListener($event, $listener)
    {
        if (is_string($listener) &&
            isset($this->actionHandlersListeners[$listener]) &&
            is_a($event, Event::class, true)) {

            remove_action($event::alias(), $this->actionHandlersListeners[$listener]);
        }

        return parent::removeListener($event, $listener);
    }

    /**
     * @param string $event
     * @return ActionEmitter
     */
    public function removeAllListeners($event)
    {
        if (is_a($event, Event::class, true)) {
            remove_all_actions($event::alias());
        }

        return parent::removeAllListeners($event);
    }

    /**
     * @param $event
     * @param $listener
     * @return \Closure
     */
    protected function actionCallback($event, $listener)
    {
        return function (...$arguments) use ($event, $listener) {
            if (Plugin::instance()->container()->has($listener)) {
                $listener = Plugin::instance()->container()->get($listener);
            } else {
                $listener = new $listener();
            }

            return $listener->handle(new $event(...$arguments));
        };
    }
}