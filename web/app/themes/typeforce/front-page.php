<?php /* Template Name: Home */ 
$args = array(
  'post_type'       => 'exhibit',
  'numberposts'     => -1,
  'posts_per_page'  => get_option( 'posts_per_page', 12 ),
);
$exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args); 


$home = get_page_by_path('home');
$headline = apply_filters('the_content', $home->post_content);
$update = apply_filters('the_content', get_post_meta($home->ID , '_cmb2_update')[0] );
?>

<div class="intro-content">
  <?= Firebelly\PostTypes\Exhibit\get_intro_slider(); ?>
  <div class="headline">
    <?= $headline ?>
  </div>
  <div class="update">
    <?= $update ?>
  </div>
</div>

<?= $exhibits ?>


