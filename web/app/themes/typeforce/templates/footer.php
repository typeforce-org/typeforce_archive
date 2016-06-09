<?php /* Template Name: Footer */ 

$footer = get_page_by_path('footer');
$description = apply_filters('the_content', $footer->post_content);
$links = apply_filters('the_content', get_post_meta($footer->ID , '_cmb2_links')[0] );
$sponsors = apply_filters('the_content', get_post_meta($footer->ID , '_cmb2_sponsors')[0] );

?>
<footer class="site-footer" role="contentinfo">
  <div class="description">
    <div class="wrap user-content">
      <?= $description ?>
    </div>
  </div>  
  <div class="links">
    <div class="wrap user-content">
      <?= $links ?>
    </div>
  </div>
  <div class="sponsors">
    <div class="wrap user-content">
      <?= $sponsors ?>
    </div>
  </div>
</footer>
