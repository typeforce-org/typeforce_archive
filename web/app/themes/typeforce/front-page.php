<?php
$args = array(
  'post_type'       => 'exhibit',
  'numberposts'     => -1,
  'orderby'         => 'rand',
  'posts_per_page'  => get_option( 'posts_per_page', 25 ),
);
$exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args);
$home = get_page_by_path('home');
$headline = apply_filters('the_content', $home->post_content);
$update = apply_filters('the_content', get_post_meta($home->ID , '_cmb2_update')[0] );
$thumb_id = get_post_thumbnail_id($post->ID);
$thumb_url = wp_get_attachment_image_src($thumb_id, 'slide' )[0];
?>

<a href="https://www.tdc.org/event/type-design-for-non-type-designers-2/" target="_blank">
  <div class="intro-content" style="background-image: url('<?= $thumb_url ?>')">
    <div class="headline">
      <div class="wrap">
        <?= $headline ?>
      </div>
    </div>
  </div>
</a>

<?= $exhibits ?>
