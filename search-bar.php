<?php

/**
 * Plugin Name:        Just Another Search Bar
 * Plugin URI:         https://github.com/XDoubleU/j-a-search-bar
 * Description:        Adds custom search bar with category filter.
 * Version:            2.0.1-alpha
 * Author:             Xander Warszawski
 * Author URI:         https://xdoubleu.com
 * License:            GNU General Public License v3.0
 * License URI:        https://github.com/XDoubleU/j-a-search-bar/blob/master/LICENSE
 * GitHub Plugin URI:  https://github.com/XDoubleU/j-a-search-bar
**/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/* Enable this plugin*/
function search_bar_enqueue_scripts() {
  /* enqueue the stylesheet.css file */
  wp_enqueue_style( 'custom-css', plugins_url( '/css/stylesheet.css', __FILE__ ), $ver = false );
  /* enqueue the main.js file */
  wp_register_script( 'custom-js', plugins_url('/js/main.js', __FILE__ ), array('jquery'), '', true);
  wp_localize_script(
    'search-main',
    'opt',
    array(
      'ajaxUrl'   => admin_url('admin-ajax.php'),
      'noResults' => esc_html__( 'No products found', 'textdomain' ),
    )
  );
}
add_action( 'wp_enqueue_scripts', 'search_bar_enqueue_scripts' );

/* Add settings*/
function search_bar_register_settings() {
  add_option( 'search_bar_category_menu', '');
  register_setting( 'search_bar_options_group', 'search_bar_category_menu', 'search_bar_callback' );
	add_option( 'search_bar_input_width', '40');
  register_setting( 'search_bar_options_group', 'search_bar_input_width', 'search_bar_callback' );
}
add_action( 'admin_init', 'search_bar_register_settings' );

function search_bar_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=JA_Search_Bar">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'search_bar_settings_link' );

function search_bar_register_options_page() {
  add_options_page('JA Search Bar', 'JA Search Bar', 'manage_options', 'JA_Search_Bar', 'search_bar_options_page');
}
add_action('admin_menu', 'search_bar_register_options_page');

function search_bar_options_page()
{
  ?>
  <div>
    <?php screen_icon(); ?>
    <h1>JA Search Bar Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'search_bar_options_group' ); ?>
      <table>
        <tr valign="top">
          <th scope="row"><label for="search_bar_category_menu">Pick your menu:</label></th>
          <td>
            <select name="search_bar_category_menu" >
	           <?php foreach(get_terms('nav_menu') as $menu){?>
	              <option value="<?php echo $menu->term_id; ?>" <?php if($menu->term_id == get_option('search_bar_category_menu')){echo 'selected';} ?>><?php echo $menu->name; ?></option><?php
              }?>
	          </select>
          </td>
        </tr>
	      <tr valign="top">
          <th scope="row"><label for="search_bar_input_width">Give search input width (%): </label></th>
  	      <td><input style="width:115px; text-align:right;" name="search_bar_input_width" value="<?php echo get_option('search_bar_input_width'); ?>"></td>
        </tr>
      </table>
      <?php  submit_button(); ?>
    </form>
  </div>
<?php
}

function display_search_bar ( $content ) {
	/*Get options*/
	$category_menu = get_option('search_bar_category_menu');
	$search_input_width = get_option('search_bar_input_width');
  ?>
	<form class=search-bar role="search" method="get" id="search" action="<?php esc_url( home_url( '/'  ) )?>">
	<?php
    $args = array(
    	'orderby'    => 'title',
        'order'      => 'ASC',
        'hide_empty' => 0,
        'parent'  => 0
    );
	?>
    <select name="product_cat" class="search-category" id="category">
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
