<?php
/*
Plugin Name: Setup of roles
Plugin URI:
Description: "Setup of roles" very simple plug-in for setup of roles and capabilities of users.
						 Allows to add and delete a role, and also to change capabilities of roles.
Version: 0.0.1
Author: Eugene Yudin
Author URI:
*/

load_plugin_textdomain('setup-of-roles', false, dirname(plugin_basename(__FILE__)).'/languages');

add_action('admin_menu', 'setup_of_roles_menu');

function setup_of_roles_menu() {
	wp_register_style( 'roles-style', plugin_dir_url(__FILE__)."css/roles.css", array(), null );		
	wp_enqueue_style( 'roles-style' );

	add_options_page( __( 'Setup of roles', 'setup-of-roles' ),
										__( 'Setup of roles', 'setup-of-roles' ),
										'manage_options', dirname(plugin_basename(__FILE__)).'/setup_of_roles_page.php');
} ?>