<?php
/**
 * CMB2 custom fields
 */

namespace Firebelly\CMB2;

/**
 * Get post options for CMB2 select
 */
function get_post_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(
        'post_type'   => 'post',
        'orderby' => 'title',
        'order' => 'ASC',
        'numberposts' => 10,
        'post_parent' => 0,
    ) );

    $posts = get_posts( $args );

    $post_options = array();
    if ( $posts ) {
        foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
        }
    }

    return $post_options;
}