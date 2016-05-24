<?php 
while (have_posts()) : the_post(); 


$exhibition = Firebelly\PostTypes\Exhibition\get_exhibition_link_from_exhibit_id($post->ID,true);
$title = get_post_meta($post->ID,'_cmb2_title',true);
$materials = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_materials',true));
$bio = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_bio',true));
$social = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_social',true));
$thumbs = Firebelly\PostTypes\Exhibit\get_exhibit_thumbnails(); 

$args = array(
  'post_type'       => 'exhibit',
  'numberposts'     => -1,
  'posts_per_page'  => get_option( 'posts_per_page', 12 ),
);
$more_exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args); 

?>

  <article <?php post_class(); ?>>
    
    <div class="header-wrap">
      <?= $thumbs ?>
      <header>
        <div class="entry-exhibition"><?= $exhibition ?></h1>
        <h1 class="entry-artist"><?php the_title(); ?></h1>
      </header>
    </div>
    <div class="content-wrap">
      <div class="entry-main">
        <h2><?= $title ?></h2>
        <div class="entry-statement"><?php the_content(); ?></div>
        <div class="entry-bio"><h2>Bio</h2><?= $bio ?></div>
      </div>
      <div class="entry-secondary">
        <div class="entry-materials">
          <h2>Materials</h2>
          <?= $materials ?>
        </div>
        <div class="entry-social">
          <h2>Social</h2>
          <?= $social; ?>
        </div>
      </div>
    </div>
    <footer class="footer-wrap">
      <nav class="exhibit-nav">
        <div class="next">
          <?php next_post_link('%link','Next Designer <svg class="icon-arrow-right" role="img"><use xlink:href="#icon-arrow-right"></use></svg>'); ?>
        </div>
        <div class="prev">

          <?php previous_post_link('%link','<svg class="icon-arrow-left" role="img"><use xlink:href="#icon-arrow-left"></use></svg> Previous Designer'); ?>
        </div>
      </nav>
    </footer>
  </article>
  <?= $more_exhibits ?>
<?php endwhile; ?>




