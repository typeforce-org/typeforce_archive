<?php 
$allyears_url = get_post_type_archive_link( 'exhibit' );

?>
<header class="site-header" role="banner">
<h1 class="title">Typeforce</h1>
  <nav class="site-nav" role="navigation">

        
    <?php
    if (has_nav_menu('primary_navigation')) :
      wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
    endif;
    ?>
    <!-- <a class="open-search"><svg class="icon-search" role="img"><use xlink:href="#icon-search"></use></svg></a> -->
    <!-- <div class="search">
      <?php get_search_form(); ?>
    </div> -->
  </nav>


  
</header>