<?php
/**
 * Template Name: Blog
 * Description: A Page Template for the blog.
 */

// Code to display Page goes here...
$context = Timber::get_context();
$page = new TimberPost();
$context['page'] = $page;

$featured = $page->get_field('feature_post');
$featured_posts_ids = array();

if( $featured )
{
    foreach( $featured as $feature )
    {
        $featured_id = $feature->ID;
        array_push($featured_posts_ids, $featured_id);
    }
}

$context['categories'] = get_categories();

$context['category'] = single_cat_title( '', false );

// var_dump($featured_posts_ids);

$args = array('post__not_in' => $featured_posts_ids, 'order' => 'ASC' );
$posts = Timber::get_posts( $args );

$context['posts'] = $posts;
$context['postLength'] = count($posts);
$templates = array( 'page-insights.twig' );
// if ( is_home() ) {
// 	array_unshift( $templates, 'home.twig' );
// }
Timber::render( $templates, $context );
