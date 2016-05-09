<?php
$args = array(
  'post_type'   => 'exhibit',
  'numberposts' => -1,
);
$exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args); 
?>
<?php get_template_part('templates/page', 'header'); ?>
<?= $exhibits ?>


