<?php 
$title = '<a href="'.get_home_url().'">Typeforce</a>';
if(is_singular('exhibit')) {
  $exhibition= Firebelly\PostTypes\Exhibit\get_exhibition_object($post->ID);
  $exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info_object($post->ID);
  $title = $exhibition_info->post_title;
  $url = get_term_link($exhibition->term_id);
  $title = '<a href="'.$url.'">'.$title.'</a>';
}
if(is_tax('exhibition')) {
  $exhibition_id = $wp_query->queried_object->term_id;
  $title = Firebelly\PostTypes\Exhibition\get_exhibition_title_from_exhibition_id($exhibition_id);
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