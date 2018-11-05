<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://studioespresso.co
 * @since             1.0.0
 * @package           Wp_Splashing
 *
 * @wordpress-plugin
 * Plugin Name:       Splashing Images
 * Plugin URI:        http://studioespresso.co
 * Description:       Unsplash.com), right in your dashboard. Add photos with one click and use them in your content right away.
 * Version:           2.1.3
 * Author:            Studio Espresso
 * Author URI:        http://studioespresso.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-splashing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-splashing-activator.php
 */
function activate_wp_splashing()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-splashing-activator.php';
    Wp_Splashing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-splashing-deactivator.php
 */
function deactivate_wp_splashing()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-splashing-deactivator.php';
    Wp_Splashing_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_splashing');
register_deactivation_hook(__FILE__, 'deactivate_wp_splashing');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-splashing.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_splashing()
{
    $plugin = new Wp_Splashing();
    $plugin->run();
}
run_wp_splashing();
