<?php /* Template Name: Header */ 

$header = get_page_by_path('header');
$headline = apply_filters('the_content', $header->post_content);
$update = apply_filters('the_content', get_post_meta($header->ID , '_cmb2_update')[0] );
$allyears_url = get_post_type_archive_link( 'exhibit' );

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
    <div class="nav-links column-wrap">
      <ul class="nav">
        <li class="menu-item">
          <a href="<?= $allyears_url ?>">All Years</a>
        </li>
        <a class="open-search">
          <li class="menu-item">
             <svg class="icon-search" role="img"><use xlink:href="#icon-search"></use></svg> 
          </li>
        </a>
      </ul>
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
      endif;
      ?>
    </div>
    <div class="search">
      <?php get_search_form(); ?>
    </div>
  </nav>
</header>