<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Contracts\Exportable;
use TotalRating\Entities\Entity;
use TotalRating\Entities\EntityManager;
use TotalRating\Exceptions\RatingNotFound;
use TotalRating\Models\Concerns\RatingSettings;
use TotalRatingVendors\TotalSuite\Foundation\Database\TableModel;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use WP_User;

/**
 * @property int $id
 * @property string $uid
 * @property string $widget_uid
 * @property string $attribute_uid
 * @property string $point_uid
 * @property int $user_id
 * @property int $entity_id
 * @property string $entity_type
 * @property array $entity_meta
 * @property float $value
 * @property string $comment
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $status
 * @property string $context
 * @property string $ip
 * @property string $agent
 * @property Attribute $attribute
 * @property Point $point
 */
class Rating extends TableModel implements Exportable
{
    use RatingSettings;

    const STATUS_ACCEPTED = 'accepted';
    const STATUS_CHANGED  = 'changed';
    const STATUS_REVOKED  = 'revoked';
    const STATUS_REJECTED = 'rejected';

    /**
     * @var string
     */
    protected $table = 'totalrating_ratings';

    /**
     * @var array
     */
    protected $types = [
        'value'       => 'float',
        'entity_meta' => 'array',
    ];

    protected $fillable = [
        'widget_uid',
        'attribute_uid',
        'point_uid',
        'entity_id',
        'entity_type',
        'entity_meta',
        'value',
        'comment',
    ];

    /**
     * @param $ratingUid
     *
     * @return Rating
     * @throws Exception
     */
    public static function byUid($ratingUid)
    {
        $rating = static::query()
                        ->where('uid', $ratingUid)
                        ->first();

        RatingNotFound::throwUnless($rating, 'Rating not found', [], 404);

        return $rating;
    }

    /**
     * @param $format
     *
     * @return array
     * @throws Exception
     */
    public function toExport($format): array
    {
        $rating = $this->withEntity()
                       ->withUser()
                       ->toArray();
        $widget = Widget::byUID($this->widget_uid);

        $data['id']   = (int) $rating['id'];
        $data['user'] = $rating['user']['name'] ?? null;

        if ($rating['entity'] instanceof Entity) {
            $data['entityType'] = $rating['entity']->getType();
            $data['entityName'] = $rating['entity']->getName();
            $data['entityUrl']  = $rating['entity']->getUrl();
        } else {
            $data['entityType'] = esc_html__('Unknown', 'totalrating');
            $data['entityName'] = esc_html__('Unknown', 'totalrating');
            $data['entityUrl']  = esc_html__('Unknown', 'totalrating');
        }

        $attribute = $widget->getRatingAttribute($this->attribute_uid);
        $point     = null;

        $data['attribute'] = null;
        $data['point']     = null;

        if ($attribute instanceof Attribute) {
            $data['attribute'] = $attribute->getLabel();
            $point             = $attribute->getPoint($this->point_uid);
        }

        if ($point instanceof Point) {
            $data['point'] = $point->label;
        }

        $data['value']      = $rating['value'];
        $data['comment']    = $rating['comment'];
        $data['created_at'] = $rating['created_at'];
        $data['status']     = $rating['status'];
        $data['ip']         = $rating['ip'];
        $data['agent']      = $rating['agent'];

        $data['context'] = $rating['context'];

        return $data;
    }

    /**
     * @return $this
     */
    public function withUser()
    {
        $user = $this->getUser();

        if ($user instanceof WP_User) {
            $user = [
                'name'   => esc_html($user->display_name),
                'avatar' => get_avatar_url($user->user_email),
            ];
        }

        $this->setAttribute('user', $user ?? null);

        return $this;
    }

    /**
     * @return $this
     */
    public function withEntity()
    {
        $this->setAttribute('entity', $this->getEntity());

        return $this;
    }

    /**
     * Convert the model for public usage (hide internal attributes).
     *
     * @return Rating
     */
    public function toPublic(): Rating
    {
        $clone = clone $this;

        $clone->deleteAttribute('id');
        $clone->deleteAttribute('agent');
        $clone->deleteAttribute('ip');
        $clone->deleteAttribute('status');
        $clone->deleteAttribute('entity');
        $clone->deleteAttribute('point');
        $clone->deleteAttribute('context');
        $clone->deleteAttribute('created_at');
        $clone->deleteAttribute('updated_at');
        $clone->deleteAttribute('deleted_at');

        return $clone;
    }

    /**
     * Get user.
     *
     * @return bool|WP_User
     */
    public function getUser()
    {
        return get_user_by('ID', $this->getAttribute('user_id'));
    }

    /**
     * Get entity.
     *
     * @return Entity|null
     */
    public function getEntity()
    {
        return EntityManager::instance()->resolve(
            $this->getAttribute('entity_id'),
            $this->getAttribute('entity_type'),
            $this->getAttribute('entity_meta') ?: []
        );
    }

    public function getWidgetCookie()
    {
        return static::getCookieName('widget', $this->widget_uid, '', '');
    }

    public function getAttributeCookie()
    {
        return static::getCookieName('widget', $this->attribute_uid, $this->entity_type, $this->entity_id);
    }

    public function getRatingsCookie()
    {
        return static::getCookieName('ratings', $this->widget_uid, $this->attribute_uid, '');
    }

    public function getEntityCookie()
    {
        return static::getCookieName('entity', $this->attribute_uid, $this->entity_type, $this->entity_id);
    }

    public function getPointCookie()
    {
        return static::getCookieName('point', $this->attribute_uid, $this->entity_type, $this->entity_id);
    }

    /**
     * @param  string  $context
     * @param  string  $attributeUid
     * @param  string  $entityType
     * @param  mixed  $entityId
     *
     * @return string
     */
    public static function getCookieName($context, $attributeUid, $entityType, $entityId)
    {
        $token = sha1($attributeUid.$entityType.$entityId);

        switch ($context) {
            case 'widget' :
            case 'attribute' :
            {
                return sprintf('tr_%s', $token);
            }
            case 'ratings' :
            {
                return sprintf('tr_%s_ratings', $token);
            }
            case 'entity' :
            {
                return sprintf('tr_%s_entity', $token);
            }
            case 'point' :
            {
                return sprintf('tr_%s_point', $token);
            }
            default :
            {
                return '';
            }
        }
    }
}
