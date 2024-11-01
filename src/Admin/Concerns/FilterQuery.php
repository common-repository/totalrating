<?php
namespace TotalRating\Admin\Concerns;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Attribute;
use WP_Query;

trait FilterQuery
{
    public function filterJoin($joins, WP_Query $query) {
        global $wpdb;

        $joins .= $wpdb->prepare(" LEFT JOIN {$wpdb->prefix}totalrating_ratings as ratings ON {$wpdb->posts}.ID = ratings.entity_id AND ratings.attribute_uid = %s", $this->params['attribute']);

        return $joins;
    }

    public function filterFields($fields, WP_Query $query) {
        $comma   = "";

        if ( $fields ) {
            $comma = ", ";
        }

        $func = $this->params['type'] === Attribute::TYPE_SCALE ? 'AVG' : 'SUM';

        $fields .= $comma . "$func(ratings.value) AS rating";

        return $fields;
    }

    public function filterOrder($order, WP_Query $query) {
        $comma   = "";

        if ( $order ) {
            $comma = ", ";
        }

        $direction = strtoupper($this->params['direction']);

        $order= "rating $direction" . $comma . $order;

        return $order;
    }

    public function filterGroup($group, WP_Query $query) {
        global $wpdb;

        $comma   = "";
        if ( $group ) {
            $comma = ", ";
        }
        $group = "{$wpdb->posts}.ID, ratings.attribute_uid" . $comma . $group;
        return $group;
    }

    public function filterDistinct($distinc, WP_Query $query) {
        global $wpdb;
        return $distinc;
    }
}