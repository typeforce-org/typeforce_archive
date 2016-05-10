<?php /* Template Name: Header */ 

$header = get_page_by_path('header');
$headline = apply_filters('the_content', $header->post_content);
$update = apply_filters('the_content', get_post_meta($header->ID , '_cmb2_update')[0] );

?>
<header class="site-header" role="banner">

  <div class="header-content">
    <?= Firebelly\PostTypes\Exhibit\get_header_slider(); ?>
    <div class="headline">
      <?= $headline ?>
    </div>
    <div class="update">
      <?= $update ?>
    </div>
  </div>
  <nav class="site-nav" role="navigation">
    <ul class="nav">
      <li class="menu-item">
        <a href="<?= get_home_url(); ?>">All Years</a>
      </li>
      <li class="menu-item">
        Search<!--<?php get_search_form(); ?> -->
      </li>
    </ul>
    <?php
    if (has_nav_menu('primary_navigation')) :
      wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
    endif;
    ?>
  </nav>
</header>