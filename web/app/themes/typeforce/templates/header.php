<?php 
$title = '<a href="'.get_home_url().'">Typeforce</a>';
?>
<header class="site-header" role="banner">

  <div class="wrapper">
    <h1 class="title"><?= $title ?></h1>

    <div class="search">
      <?php get_search_form(); ?>
    </div>
    <button class="search-toggle"></button>
    <nav class="site-nav" role="navigation">

      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
      endif;
      ?>
    </nav>
  </div>

</header>