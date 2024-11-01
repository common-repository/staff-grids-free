<?php

/*
Plugin Name: Staff grids free
Plugin URI: 
Version: 1.03
Description: Display your staff grid in a cool way
Author: Manu225
Author URI: 
Network: false
Text Domain: staff-grids-free
Domain Path: 
*/


register_activation_hook( __FILE__, 'staff_grids_install' );
register_uninstall_hook(__FILE__, 'staff_grids_desinstall');

function staff_grids_install() {

	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$staff_grids_table = $wpdb->prefix . "staff_grids";
	$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

	$sql = "
        CREATE TABLE `".$staff_grids_table."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          name varchar(50) NOT NULL,          
          show_name int(1) NOT NULL,
          text_size int(3) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    dbDelta($sql);

    $sql = "
        CREATE TABLE `".$staff_grids_members_table."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          id_grid int(3) NOT NULL,
          name varchar(100) NOT NULL, 
          photo varchar(500) NOT NULL,         
          job varchar(100) NOT NULL,
          mail varchar(50) NOT NULL,
          tel varchar(20) NOT NULL,
          link varchar(500) NOT NULL,
          blank int(1) NOT NULL,
          `order` int(1) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    dbDelta($sql);

}

function staff_grids_desinstall() {

	global $wpdb;

	$staff_grids_table = $wpdb->prefix . "staff_grids";
	$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

	//suppression des tables
	$sql = "DROP TABLE ".$staff_grids_table.";";
	$wpdb->query($sql);
	$sql = "DROP TABLE ".$staff_grids_members_table.";";
	$wpdb->query($sql);
}

add_action( 'admin_menu', 'register_staff_grid_menu' );

function register_staff_grid_menu() {

	add_menu_page('Staff grids free', 'Staff grids free', 'edit_pages', 'staff_grids', 'staff_grids', plugins_url('images/icon.png', __FILE__), 33);

}

add_action('admin_print_styles', 'staff_grids_css' );
function staff_grids_css() {
    wp_enqueue_style( 'StaffGridsStylesheet', plugins_url('css/admin.css', __FILE__) );
}

function staff_grids() {

	if (is_admin()) {

		global $wpdb;

		$staff_grids_table = $wpdb->prefix . "staff_grids";
		$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

		if(is_numeric($_GET['id']))
		{
			//on récupère toutes les cards
			$grid = $wpdb->get_row("SELECT * FROM ".$staff_grids_table." WHERE id=".$_GET['id']);
			$members = $wpdb->get_results("SELECT * FROM ".$staff_grids_members_table." WHERE id_grid = ".$_GET['id']." ORDER BY `order` ASC", OBJECT);
			include(plugin_dir_path( __FILE__ ) . 'views/members.php');
		}
		else
		{

			if(sizeof($_POST) > 0)
			{
				if(empty($_POST['name']))
					echo '<h2>You must enter a name!</h2>';
				else if(!is_numeric($_POST['id'])) //nouvelle grille
				{
					check_admin_referer( 'new_staff_grid' );
					$show_name = isset($_POST['show_name']) ? 1 : 0;
					$query = $wpdb->prepare( "INSERT INTO ".$staff_grids_table." (`name`, `text_size`, `show_name`) VALUES (%s, %d, %d)", stripslashes_deep($_POST['name']), $_POST['text_size'], $show_name);
					$wpdb->query($query);
				}
				else //mise à jour d'une grille
				{
					check_admin_referer( 'update_staff_grid_'.$_POST['id'] );
					$show_name = isset($_POST['show_name']) ? 1 : 0;
					$query = $wpdb->prepare( "UPDATE ".$staff_grids_table." SET `name` = %s, `text_size` = %d, `show_name` = %d WHERE id = %d",
					stripslashes_deep($_POST['name']), $_POST['text_size'], $show_name, $_POST['id'] );
					$wpdb->query($query);
				}
			}	
			//on récupère toutes les grilles
			$grids = $wpdb->get_results("SELECT * FROM ".$staff_grids_table);
			include(plugin_dir_path( __FILE__ ) . 'views/grids.php');
		}
	}
}

//Ajax : ajout d'un membre
add_action( 'wp_ajax_staff_grid_add_member', 'staff_grid_add_member' );
function staff_grid_add_member()
{
	check_ajax_referer( 'staff_grid_add_member' );

	if (is_admin()) {

		if(is_numeric($_POST['id_grid']))
		{
			global $wpdb;
			$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

			$max_order = $wpdb->get_row( $wpdb->prepare( "SELECT MAX(`order`) as max_order FROM ".$staff_grids_members_table." WHERE id_grid = %d", $_POST['id'] ));
			if($max_order)
				$max_order = ($max_order->max_order+1);
			else
				$max_order = 1;

			$query = "INSERT INTO ".$staff_grids_members_table." (`id_grid`, `name`, `photo`, `job`, `mail`, `tel`, `link`, `blank`, `order`)
			VALUES (%d, %s, %s, %s, %s, %s, %s, %d, %d)";
			$query = $wpdb->prepare($query, $_POST['id_grid'], $_POST['name'], $_POST['photo'], $_POST['job'], $_POST['mail'], $_POST['tel'],$_POST['link'], $_POST['blank'], $max_order);
			$wpdb->query($query);
		}

	}
	wp_die();
}

//Ajax : edition d'un membre
add_action( 'wp_ajax_staff_grid_save_member', 'staff_grid_save_member' );
function staff_grid_save_member()
{
	check_ajax_referer( 'staff_grid_save_member' );

	if (is_admin()) {

		if(is_numeric($_POST['id']))
		{
			global $wpdb;
			$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

			$query = "UPDATE ".$staff_grids_members_table." SET `name` = %s, `photo` = %s, `job` = %s, `mail` = %s, `tel` = %s, `link` = %s, `blank` = %d WHERE id = %d";
			$query = $wpdb->prepare($query, $_POST['name'], $_POST['photo'], $_POST['job'], $_POST['mail'], $_POST['tel'], $_POST['link'], $_POST['blank'], $_POST['id']);
			$wpdb->query($query);
		}

	}
	wp_die();
}

//Ajax : suppression d'un membre
add_action( 'wp_ajax_staff_grid_remove_member', 'staff_grid_remove_member' );
function staff_grid_remove_member()
{
	check_ajax_referer( 'staff_grid_remove_member' );

	if (is_admin()) {

		if(is_numeric($_POST['id']))
		{
			global $wpdb;
			$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

			$query = "DELETE FROM ".$staff_grids_members_table." WHERE id = %d";
			$query = $wpdb->prepare($query, $_POST['id']);
			$wpdb->query($query);
		}

	}
	wp_die();
}

//Ajax : changement de position d'un membre
add_action( 'wp_ajax_staff_grid_member_order', 'staff_grid_member_order' );

function staff_grid_member_order() {

	check_ajax_referer( 'staff_grid_member_order' );

	if (is_admin()) {

		global $wpdb;
		$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

		if(is_numeric($_POST['id']) && is_numeric($_POST['order']))
		{
			$grid = $wpdb->get_row( $wpdb->prepare( "SELECT id_grid, `order` FROM ".$staff_grids_members_table." WHERE id = %d", $_POST['id'] ));
			if($_POST['order'] > $grid->order)
				$wpdb->query( $wpdb->prepare( "UPDATE ".$staff_grids_members_table." SET `order` = `order` - 1 WHERE id_grid = %d AND `order` <= %d AND `order` > %d", $grid->id_grid, $_POST['order'], $grid->order ));
			else
				$wpdb->query( $wpdb->prepare( "UPDATE ".$staff_grids_members_table." SET `order` = `order` + 1 WHERE id_grid = %d AND `order` >= %d AND `order` < %d", $grid->id_grid, $_POST['order'], $grid->order ));
			$wpdb->query( $wpdb->prepare( "UPDATE ".$staff_grids_members_table." SET `order` = %d WHERE id = %d", $_POST['order'], $_POST['id'] ));
			
		}
		wp_die();
	}
}

//Ajax : suppression d'une playlist
add_action( 'wp_ajax_remove_staff_grid', 'remove_staff_grid_callback' );
function remove_staff_grid_callback() {

	check_ajax_referer( 'remove_staff_grid' );

	if (is_admin()) {

		global $wpdb;

		$staff_grids_table = $wpdb->prefix . "staff_grids";
		$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";

		if(is_numeric($_POST['id']))
		{
			//supprime toutes les membres
			$query = $wpdb->prepare( 
				"DELETE FROM ".$staff_grids_members_table."
				 WHERE id_grid=%d", $_POST['id']
			);
			$res = $wpdb->query( $query	);

			//supprime la grille
			$query = $wpdb->prepare( 
				"DELETE FROM ".$staff_grids_table."
				 WHERE id=%d", $_POST['id']
			);
			$res = $wpdb->query( $query	);

		}
		wp_die();
	}
}

add_shortcode('staff-grid', 'display_staff_grid');
function display_staff_grid($atts) {

	if(is_numeric($atts['id']))
	{

		global $wpdb;

		$staff_grids_table = $wpdb->prefix . "staff_grids";
		$staff_grids_members_table = $wpdb->prefix . "staff_grids_members";
		$query = "SELECT * FROM ".$staff_grids_table." WHERE id = %d";
		$query = $wpdb->prepare( $query, $atts['id'] );
		$grid = $wpdb->get_row($query);

		if($grid)
		{
			//récupères tous les membres
			$query = "SELECT * FROM ".$staff_grids_members_table." WHERE id_grid = %d ORDER BY `order` ASC";
			$query = $wpdb->prepare($query, $atts['id']);
			$members = $wpdb->get_results($query);

			wp_enqueue_style( 'staff_grid_grid_css', plugins_url( 'css/grid.css', __FILE__ ));
			wp_enqueue_script( 'staff_grid_grid_js', plugins_url( 'js/grid.js', __FILE__ ));
			$view = plugin_dir_path( __FILE__ ) . 'views/tpl/grid.php';
			
			ob_start();
			include( $view );
			$playlist_html = ob_get_clean();
			
			return $playlist_html;
		}
		else
			return "Error : staff grid id ".$atts['id'].' not found!';

	}
	else
		return 'Wrong ID format!';

}

add_action( 'wp_enqueue_scripts', function() {	wp_enqueue_script( 'jquery' ); });	

function staff_grids_load_scripts() {

    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

}

add_action( 'admin_enqueue_scripts', 'staff_grids_load_scripts' );	