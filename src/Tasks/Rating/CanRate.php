<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Exceptions\LimitationReached;
use TotalRating\Exceptions\UnacceptedEntity;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRating\Plugin;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\ValidateInput;
use WP_User;

/**
 * Class CanRate
 *
 * @package TotalRating\Tasks\Rating
 * @method static boolean invoke(Widget $widget, Entity $entity, array $data)
 * @method static boolean invokeWithFallback($fallback, Widget $widget, Entity $entity, array $data)
 */
class CanRate extends Task
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var WP_User
     */
    protected $user;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  Entity  $entity
     * @param  array  $data
     */
    public function __construct(Widget $widget, Entity $entity, array $data)
    {
        $this->widget = $widget;
        $this->entity = $entity;
        $this->data   = $data;
        $this->user   = wp_get_current_user();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function validate()
    {
        return ValidateInput::invoke(
            [
                'attribute_uid' => 'required|string',
                'ip'            => 'required|string',
                'agent'         => 'required|string',
            ],
            $this->data
        );
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function execute()
    {
        $entities = $this->widget->getEntities();

        if (!$this->widget->allowOtherContext()) {
            UnacceptedEntity::throwUnless(in_array($this->entity->getType(), $entities, true));
        }

        $this->checkUserLimitation();
        $this->checkCookieLimitation();
        $this->checkIpLimitation();

        return true;
    }

    /**
     * @throws Exception
     */
    public function checkCookieLimitation()
    {
        if ($this->widget->isLimitationByCookiesEnabled()) {
            $cookies = CookieJar::instance();

            $ratingUid = $cookies->get(
                Rating::getCookieName(
                    'widget',
                    $this->data['attribute_uid'],
                    $this->entity->getType(),
                    $this->entity->getId()
                )
            );

            $isProactiveCheckEnabled = Plugin::options('advanced.proactiveCheck.enabled');

            if ($isProactiveCheckEnabled && !empty($ratingUid) && strlen($ratingUid) === 36) {
                try {
                    $rating = Rating::byUid($ratingUid);
                } catch (\Exception $exception) {
                    return;
                }
            }

            $ratingCount = (int) $cookies->get(
                Rating::getCookieName(
                    'ratings',
                    $this->widget->uid,
                    $this->data['attribute_uid'],
                    ''
                ),
                0
            );

            LimitationReached::throwIf(
                $ratingCount >= $this->widget->getLimitationPerSession(),
                'Rating not allowed',
                $this->data
            );

            if ($this->entity) {
                $ratingsEntity = $cookies->get(
                    Rating::getCookieName(
                        'entity',
                        $this->data['attribute_uid'],
                        $this->entity->getType(),
                        $this->entity->getId()
                    ),
                    0
                );

                LimitationReached::throwIf(
                    $ratingsEntity >= $this->widget->getLimitationPerEntity(),
                    'Rating not allowed'
                );
            }
        }
    }

    /**
     * @throws Exception
     */
    public function checkIpLimitation()
    {
        if ($this->widget->isLimitationByIpEnabled()) {
            $ratingCount = Rating::query()
                                 ->where('widget_uid', $this->widget->uid)
                                 ->where('attribute_uid', $this->data['attribute_uid'])
                                 ->where('ip', $this->data['ip'])
                                 ->count();

            LimitationReached::throwIf(
                $ratingCount >= $this->widget->getLimitationPerSession(),
                'Rating not allowed'
            );

            if ($this->entity) {
                $ratingsEntity = Rating::query()
                                       ->where('widget_uid', $this->widget->uid)
                                       ->where('attribute_uid', $this->data['attribute_uid'])
                                       ->where('entity_id', $this->entity->getId())
                                       ->where('entity_type', $this->entity->getType())
                                       ->where('ip', $this->data['ip'])
                                       ->count();


                LimitationReached::throwIf(
                    $ratingsEntity >= $this->widget->getLimitationPerEntity(),
                    'Rating not allowed'
                );
            }
        }
    }

    /**
     * @throws Exception
     */
    public function checkUserLimitation()
    {
        // Check limitation first
        if ($this->widget->isLimitationByUserEnabled()) {
            if (!is_user_logged_in()) {
                Exception::throw('Anonymous rating is not allowed.');
            }

            // Check roles
            if ($this->widget->getSettings('limitations.user.options.specificRoles', false)) {
                $roles = (array) $this->widget->getSettings('limitations.user.options.roles', []);
                $roles = array_keys(array_filter($roles));

                $match = array_intersect((array) $this->user->roles, $roles);
                Exception::throwIf(empty($match), 'You are not allowed to rate.');
            }

            // Check entity, if any (prioritized)
            if ($this->entity) {
                $ratingsEntity = Rating::query()
                                       ->where('widget_uid', $this->widget->uid)
                                       ->where('attribute_uid', $this->data['attribute_uid'])
                                       ->where('entity_id', $this->entity->getId())
                                       ->where('status', Rating::STATUS_ACCEPTED)
                                       ->where('entity_type', $this->entity->getType())
                                       ->where('user_id', $this->user->ID)
                                       ->count();


                LimitationReached::throwIf(
                    $ratingsEntity >= $this->widget->getLimitationPerEntity(),
                    'Rating not allowed'
                );
            } else {
                // Check user
                $ratingCount = Rating::query()
                                     ->where('widget_uid', $this->widget->uid)
                                     ->where('attribute_uid', $this->data['attribute_uid'])
                                     ->where('status', Rating::STATUS_ACCEPTED)
                                     ->where('user_id', $this->user->ID)
                                     ->count();

                LimitationReached::throwIf(
                    $ratingCount >= $this->widget->getLimitationPerSession(),
                    'Rating not allowed'
                );
            }
        }
    }
}
