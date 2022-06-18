<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Madcow_Instructors
 *
 * @wordpress-plugin
 * Plugin Name:       Mad Cow Web - Chi Instructors
 * Plugin URI:        https://madcowweb.com/madcow-instructors-uri/
 * Description:       Mad Cow Web - Custom Plugin for Instructor Management
 * Version:           1.0.0
 * Author:            Mad Cow Web
 * Author URI:        https://madcowweb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       madcow-instructors
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MADCOW_INSTRUCTORS_VERSION', '1.0.19' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-madcow-instructors-activator.php
 */
function activate_madcow_instructors() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-madcow-instructors-activator.php';
	Madcow_Instructors_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-madcow-instructors-deactivator.php
 */
function deactivate_madcow_instructors() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-madcow-instructors-deactivator.php';
	Madcow_Instructors_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_madcow_instructors' );
register_deactivation_hook( __FILE__, 'deactivate_madcow_instructors' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-madcow-instructors.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_madcow_instructors() {

	$plugin = new Madcow_Instructors();
	$plugin->run();

}
run_madcow_instructors();
