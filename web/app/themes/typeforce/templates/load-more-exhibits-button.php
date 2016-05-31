<!-- <?php
  use Firebelly\Utils;
  global $wp_query;
  $exhibition_id = isset($wp_query->queried_object->term_id) ? $wp_query->queried_object->term_id : '';
  $search_query = get_search_query();
  $per_page = get_query_var( 'posts_per_page', get_option( 'posts_per_page', 25 ));
  $orderby = get_query_var('orderby');
  //get total post count for all query
  if($exhibition_id) {
    $term = get_term_by('id',$exhibition_id,'exhibition');
    $total_posts = $term->count;
  } elseif ($search_query) {
    $total_posts = $wp_query->found_posts;
  } else {
    $total_posts = wp_count_posts('exhibit')->publish;
  }
  $total_pages = ceil( $total_posts / $per_page);


//get all ids from post
  $post__not_in = '';
  if($orderby === 'rand') {
    global $query_string;
    $the_query = new WP_Query( $query_string . '&fields=ids' );
    if(isset($the_query->posts) && !empty($the_query->posts)){
      foreach((array) $the_query->posts as $id) {
        $post__not_in .= $id.',';
      }
    }
  }
?>



?>

<div class="load-more" data-page-at="1" data-exhibition-id="<?= $exhibition_id ?>" data-search-query="<?= $search_query ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>" data-orderby="<?= $orderby ?>" data-post--not-in="<?= $post__not_in ?>">
  <a class="no-ajaxy" href="#">Load More</a>
</div> -->