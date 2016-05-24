<?php 
$title = '<a href="'.get_home_url().'">Typeforce</a>';
if(is_singular('exhibit')) {
  $exhibition_id = Firebelly\PostTypes\Exhibition\get_exhibition_object($post->ID)->term_id;
  $title = '<a href="'.get_term_link($exhibition_id).'">'.get_term_meta($exhibition_id,'_cmb2_full_title',true).'</a>';
}
if(is_tax('exhibition')) {
  $exhibition_id = $wp_query->queried_object->term_id;
  $title = '<a href="'.get_term_link($exhibition_id).'">'.get_term_meta($exhibition_id,'_cmb2_full_title',true).'</a>';
}




?>
<header class="site-header" role="banner">
  <h1 class="title"><?= $title ?></h1>
  <nav class="site-nav" role="navigation">

    <?php
    if (has_nav_menu('primary_navigation')) :
      wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
    endif;
    ?>
  </nav>

  <div class="search">
    <?php get_search_form(); ?>
  </div>

  
</header>