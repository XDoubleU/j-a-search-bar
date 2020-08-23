<?php

/**
 * Plugin Name:        Search Header
 * Plugin URI:         https://github.com/XDoubleU/search-header-plugin-astra
 * Description:        Adds custom search bar to header
 * Version:            1.0
 * Author:             Xander Warszawski
 * Author URI:         https://xdoubleu.com
 * License:            GNU General Public License v3.0
 * License URI:        https://github.com/XDoubleU/search-header-plugin-astra/blob/master/LICENSE
 * GitHub Plugin URI:  https://github.com/XDoubleU/search-header-plugin-astra
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/* Enable this plugin*/
function search_bar_css_enqueue_scripts() {

    /* enqueue the custom.css file */
    wp_enqueue_style( 'custom-css', plugins_url( '/css/stylesheet.css', __FILE__ ), $ver = false );

	// Register script, dependant on jQuery
  /*
	wp_register_script('custom-js',plugin_dir_url( __FILE__ ).'/js/javascript.js', array('jquery'));
	// Enqueue the Script for output onto page:
	wp_enqueue_script('custom-js');
  */

}
add_action( 'wp_enqueue_scripts', 'search_bar_css_enqueue_scripts' );

/* Add settings*/
function search_header_register_settings() {
   	add_option( 'search_header_category_menu', '');
   	register_setting( 'search_header_options_group', 'search_header_category_menu', 'search_header_callback' );
	add_option( 'search_header_input_width', '40');
   	register_setting( 'search_header_options_group', 'search_header_input_width', 'search_header_callback' );
}
add_action( 'admin_init', 'search_header_register_settings' );

function search_header_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=search_header">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'search_header_settings_link' );

function search_header_register_options_page() {
  add_options_page('Search Header', 'Search Header', 'manage_options', 'search_header', 'search_header_options_page');
}
add_action('admin_menu', 'search_header_register_options_page');

function search_header_options_page()
{
  ?>
  <div>
  <?php screen_icon(); ?>
  <h1>Search Header Settings</h1>
  <form method="post" action="options.php">
  <?php settings_fields( 'search_header_options_group' ); ?>
  <table>
  <tr valign="top">
  <th scope="row"><a href="https://github.com/XDoubleU/search-header-plugin-astra/projects/1">GitHub Project</a>

  </tr>
  <tr valign="top">
  <th scope="row"><label for="search_header_category_menu">Pick your menu:</label></th>
  <td><select name="search_header_category_menu" >
	  	<?php foreach(get_terms('nav_menu') as $menu){?>
	  <option value="<?php echo $menu->term_id; ?>" <?php if($menu->term_id == get_option('search_header_category_menu')){echo 'selected';} ?>><?php echo $menu->name; ?></option><?php
  }?>

	  </select></td></tr>
	<tr valign="top">
  <th scope="row"><label for="search_header_input_width">Give search input width (%): </label></th>
  	<td><input style="width:115px; text-align:right;" name="search_header_input_width" value="<?php echo get_option('search_header_input_width'); ?>"></td>

  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
}



function display_search_bar ( $content ) {
	/*Get options*/
	$category_menu = get_option('search_header_category_menu');
	$search_input_width = get_option('search_header_input_width');


    ?>
		<form class=search-bar role="search" method="get" id="searchform" action="<?php esc_url( home_url( '/'  ) )?>">
	<?php
    $args = array(
    	'orderby'    => 'title',
        'order'      => 'ASC',
        'hide_empty' => 0,
        'parent'  => 0
    );
	?>
		<select name="product_cat" class="search-category">
		<option value="">ALLES</option>
	<?php

    $menu = wp_get_nav_menu_items( $category_menu, $args );
    $count = count($menu);

    if ( $count > 0 ){
    	foreach ( $menu as $menu_item ) {
			$menu_item_id = $menu_item->ID;
    		$menu_item_title = $menu_item->title;
    		$menu_item_url = $menu_item->url;
			$menu_item_slug = chop(str_replace("https://www.toplampen.be/product-categorie/", "",$menu_item_url), "/");

            if ($menu_item->menu_item_parent == 0) { ?>
				<option value="<?php echo $menu_item_slug; ?>"><?php echo $menu_item_title; ?></option><?php
            }
		}
	}

	?>
		</select>
			<input style="width:<?php echo $search_input_width;?>%;" class="search-input" type="text" value="<?php get_search_query()?>" name="s" id="s" placeholder="Zoeken..." />
		<input class="search-submit" type="submit" id="searchsubmit" value="<?php echo esc_attr__( 'Search', 'woocommerce' );?>" />
		<input type="hidden" name="post_type" value="product" />
		</form>
	<?php
	echo $settings;

}
add_action( 'astra_masthead_content', 'display_search_bar' );
