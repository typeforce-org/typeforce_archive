<?php 
while (have_posts()) : the_post(); 

$exhibition_term = wp_get_post_terms($post->ID,'exhibition')[0];
$exhibition_id = $exhibition_term->term_id;

$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info($exhibition_id,true); 
$exhibition = $exhibition_term->name;
$title = get_post_meta($post->ID,'_cmb2_title',true);
$materials = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_materials',true));
$bio = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_bio',true));
$social = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_social',true));
$thumbs = Firebelly\PostTypes\Exhibit\get_exhibit_thumbnails(); 

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
        <div class="entry-materials"><?= $materials ?></div>
        <div class="entry-bio"><h2>Bio</h2><?= $bio ?></div>
      </div>
      <div class="entry-social">
        <h2>Social</h2>
        <?= $social; ?>
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
<?php endwhile; ?>




