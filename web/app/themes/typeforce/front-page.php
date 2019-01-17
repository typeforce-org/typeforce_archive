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

<!-- cowboy wildstyle -->
<div class="intro-content bigclicky" style="height:auto;padding:0">
  <div class="video">
    <iframe src="https://player.vimeo.com/video/311559805?background=1;autoplay=1&autopause=0&loop=1&title=0&byline=0&portrait=0" width="960" height="540" frameborder="0"></iframe>
  </div>
  <div class="headline" style="height:100%;">
    <div class="wrap">
      <?= $headline ?>
    </div>
  </div>
</div>
<!-- repo man -->

<?= $exhibits ?>
