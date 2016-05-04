<?php /* Template Name: Header */ 

$header = get_page_by_path('header');
$headline = apply_filters('the_content', $header->post_content);
$update = apply_filters('the_content', get_post_meta($header->ID , '_cmb2_update')[0] );

?>
<header class="site-header" role="banner">
  <?= Firebelly\PostTypes\Exhibit\get_header_slider(); ?>
  <div class="headline">
    <?= $headline ?>
  </div>
  <div class="update">
    <?= $update ?>
  </div>
  <nav class="site-nav" role="navigation">
      <?php get_search_form(); ?>
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
      endif;
      ?>
    </nav>
</header>