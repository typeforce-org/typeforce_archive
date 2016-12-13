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

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-998109-32', 'auto');
  ga('send', 'pageview');

</script>