<?php

// Code to display Page goes here...
$context = Timber::get_context();

// var_dump($featured_posts_ids);

$args = array('order' => 'ASC' );
$posts = Timber::get_posts( $args );

$context['page'] = new Timber\Post();
$context['posts'] = $posts;
$context['foo'] = 'bar';

$context['archives'] = new TimberArchives();

$context['categories'] = get_categories();

$templates = array( 'page-news-archive.twig' );
// if ( is_home() ) {
// 	array_unshift( $templates, 'home.twig' );
// }
Timber::render( $templates, $context );