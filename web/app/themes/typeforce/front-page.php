<?php /* Template Name: Home */ 
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
$intro_slider = Firebelly\PostTypes\Intro\get_intro_slider();

  $all_exhibits = get_posts($args);
  foreach ( $all_exhibits as $an_exhibit ) {
    update_post_meta($an_exhibit->ID, '_cmb2_type', 'exhibit');
  }


?>



<div class="intro-content">
  <?= $intro_slider ?>
  <div class="headline">
    <?= $headline ?>
  </div>
</div>

<?= $exhibits ?>


