<?php

add_filter( 'auto_update_plugin', '__return_true' );

if ( function_exists('acf_add_options_page') ) {

	acf_add_options_page();

}

function remove_menus(){

  // remove_menu_page( 'index.php' );                  //Dashboard
  // remove_menu_page( 'edit.php' );                   //Posts
  // remove_menu_page( 'upload.php' );                 //Media
  // remove_menu_page( 'edit.php?post_type=page' );    //Pages
  // remove_menu_page( 'edit-comments.php' );          //Comments
  // remove_menu_page( 'themes.php' );                 //Appearance
  // remove_menu_page( 'plugins.php' );                //Plugins
  // remove_menu_page( 'users.php' );                  //Users
  // remove_menu_page( 'tools.php' );                  //Tools
  // remove_menu_page( 'options-general.php' );        //Settings

}

/**
 * Enqueue scripts and styles.
 */
function scripts() {

	// wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );

	// wp_enqueue_style( 'main-style', get_stylesheet_uri() );
	wp_enqueue_style( 'main-style', get_template_directory_uri() . '/dist/app.css' );
	wp_enqueue_style( 'objectfitcss', get_template_directory_uri() . '/dist/vendor/polyfill.object-fit.min.css' );

	wp_deregister_script( 'jquery' );
	// wp_deregister_script( 'jquery-ui' );

	// Register the library again from Google's CDN
	// wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', array(), null );
	wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyD6hD_-KrPp5IbBd2Dyg8L_7RV5FWdghX4', array(), null, true );
	// wp_register_script( 'jquery', get_template_directory_uri() . "/bower_components/jquery/dist/jquery.min.js", array(), true );

	// wp_enqueue_script( 'jquery' );

	// wp_enqueue_script( 'youtubeapi', 'http://www.youtube.com/iframe_api', array(), null, true );

	// wp_enqueue_script( 'googlemapsapijs', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array('jquery') );
	wp_enqueue_script( 'pace', get_template_directory_uri() . '/dist/vendor/pace.min.js', array(), null, false );
	wp_enqueue_script( 'objectfitjs', get_template_directory_uri() . '/dist/vendor/polyfill.object-fit.min.js', array(), null, false );
	wp_enqueue_script( 'picturefill', get_template_directory_uri() . '/dist/vendor/picturefill.min.js', array(), null, false );
	// wp_enqueue_script( 'fadingEffect', get_template_directory_uri() . '/dist/vendor/fullpage.fadingEffect.min.js', array(), null, false );
	// wp_enqueue_script( 'scrollOverflow', get_template_directory_uri() . '/dist/vendor/scrolloverflow.min.js', array(), null, false );
	// wp_enqueue_script( 'scrollOverflowReset', get_template_directory_uri() . '/dist/vendor/fullpage.scrollOverflowReset.min.js', array(), null, false );
	wp_enqueue_script( 'sitejs', get_template_directory_uri() . '/dist/app.js', array(), null, true );

}

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});

	add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});

	return;
}

function register_my_menus() {
	register_nav_menus(
		array(
			'main_menu_1' => __( 'Main Menu 1' ),
			'menu_footer' => __( 'Footer Menu' )
		)
	);
}

// function register_post_types() {
//
//
//
// 	/*
// 		register_post_type( 'work',
// 			  array(
// 			    'labels' => array(
// 			      'name' => __( 'Works' ),
// 			      'singular_name' => __( 'Work' ),
// 			      'add_new_item' => __( 'Add New Work' ),
// 			      'edit_item' => __( 'Edit Work Item' ),
// 			      'new_item' => __( 'New Work' ),
// 			      'view_item' => __( 'View Work' ),
// 			      'search_items' => __( 'Search Works' ),
// 			      'not_found' => __( 'No Works found' ),
// 			      'not_found_in_trash' => __( 'No Works found in Trash' )
// 			    ),
// 			    'menu_icon' => 'dashicons-art',
// 			    'has_archive' => false,
// 			    'menu_position' => 20,
// 					'show_in_nav_menus' => true,
// 			    'public' => true,
// 			    'supports' => array(
// 			      'title'
// 			    ),
// 			    'rewrite' => array(
// 			      'slug' => 'works',
// 						'revisions'
// 			    )
// 			  )
// 			);
// 			*/
//
// }
// add_action('init', 'register_custom_posts_init');

function register_taxonomies() {
	//this is where you can register custom taxonomies
}

function oEmbedAddParams($iframeHTML, $params) {

	// use preg_match to find iframe src
	preg_match('/src="(.+?)"/', $iframeHTML, $matches);
	$src = $matches[1];

	$new_src = add_query_arg($params, $src);

	$iframeHTML= str_replace($src, $new_src, $iframeHTML);


	// add extra attributes to iframe html
	$attributes = 'frameborder="0"';

	$iframeHTML= str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframeHTML);


	echo $iframeHTML;

}

// add some extra info to the post list in the cms
function add_post_type_columns($columns) {

	$col_count = 0;
	$new_col_destination = 2;
	$new_columns = array();

 	foreach( $columns as $key=>$value ) {
 		//var_dump($key,$column);
 		if ( $col_count == $new_col_destination) {
 			//array('post_type' => __('Post type')
 			$new_columns["post_type"] = __('Post type');
 		}

 		$new_columns[$key] = $value;

 		$col_count++;
 	}
 	return $new_columns;

}
add_filter('manage_posts_columns' , 'add_post_type_columns');

function custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'post_type':
			//$post = get_post($post_id);
			//var_dump(get_field('content_page_post_type',$post_id));
			$custom_page_post_type = get_field('content_page_post_type',$post_id)[0];
			//echo "test";
			//var_dump($custom_page_post_type);
			echo ucfirst(str_replace("_", " ", $custom_page_post_type["acf_fc_layout"]));
			break;

	}
}
add_action( 'manage_posts_custom_column' , 'custom_columns', 10, 2 );


Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_action( 'init', 'register_my_menus' );

		add_action( 'admin_menu', 'remove_menus' );
		add_action( 'wp_enqueue_scripts', 'scripts' );

		parent::__construct();
	}

	function register_post_types() {
		//this is where you can register custom post types


// 		$podcast_labels = array(
// 				'name'               => 'Podcasts',
// 				'singular_name'      => 'Podcast',
// 				'menu_name'          => 'Podcasts'
// 		);
// 		$podcasts_args = array(
// 				'labels'             => $podcast_labels,
// 				'public'             => true,
// 				'capability_type'    => 'post',
// 				'has_archive'        => true,
// 				'supports'           => array( 'title', 'editor', 'revisions' )
// 		);
// 		register_post_type('podcasts', $podcasts_args);

// 		// Register Video
// 		$video_labels = array(
// 				'name'               => 'Videos',
// 				'singular_name'      => 'Video',
// 				'menu_name'          => 'Videos'
// 		);
// 		$videos_args = array(
// 				'labels'             => $video_labels,
// 				'public'             => true,
// 				'capability_type'    => 'post',
// 				'has_archive'        => true,
// 				'supports'           => array( 'title', 'editor', 'revisions' )
// 		);
// 		register_post_type('videos', $videos_args);

// 		// Slider
// 		$slider_labels = array(
// 				'name'               => 'Sliders',
// 				'singular_name'      => 'Slider',
// 				'menu_name'          => 'Sliders'
// 		);
// 		$sliders_args = array(
// 				'labels'             => $slider_labels,
// 				'public'             => true,
// 				'capability_type'    => 'post',
// 				'has_archive'        => true,
// 				'supports'           => array( 'title', 'editor', 'revisions' )
// 		);
// 		register_post_type('sliders', $sliders_args);

// 		// Quote Video
// 		$quote_labels = array(
// 				'name'               => 'Quotes',
// 				'singular_name'      => 'Quote',
// 				'menu_name'          => 'Quotes'
// 		);
// 		$quotes_args = array(
// 				'labels'             => $quote_labels,
// 				'public'             => true,
// 				'capability_type'    => 'post',
// 				'has_archive'        => true,
// 				'supports'           => array( 'title', 'editor', 'revisions' )
// 		);
// 		register_post_type('quotes', $quotes_args);

// 		// Image Video
// 		$image_labels = array(
// 				'name'               => 'Single Images',
// 				'singular_name'      => 'Single Image',
// 				'menu_name'          => 'Single Images'
// 		);
// 		$images_args = array(
// 				'labels'             => $image_labels,
// 				'public'             => true,
// 				'capability_type'    => 'post',
// 				'has_archive'        => true,
// 				'supports'           => array( 'title', 'editor', 'revisions' )
// 		);
// 		register_post_type('single-images', $images_args);



	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies

		// podcast taxonomy used to link series of podcasts together
		register_taxonomy(
				'podcast_series',
				'post',
				array(
						'label' => __( 'Podcast series' ),
						'rewrite' => array( 'slug' => 'podcast_series' ),
// 						'capabilities' => array(
// 								'assign_terms' => 'edit_guides',
// 								'edit_terms' => 'publish_guides'
// 						)
				)
		);
	}

	function add_to_context( $context ) {
// 		$context['foo'] = 'bar';
// 		$context['stuff'] = 'I am a value set in your functions.php file';
// 		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		$context['menu_main'] = new TimberMenu('main_menu_1');
		$context['menu_footer'] = new TimberMenu('menu_footer');
		$context['options'] = get_fields('options');
		$context['site'] = $this;
		return $context;
	}

	function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addExtension( new Twig_Extension_Debug() );
		$twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}

}

new StarterSite();
