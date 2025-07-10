<?php
/**
 * Plugin Name: ترتيب الكلمات المفتاحية في جوجل
 * Plugin URI: https://www.fjomah.com/
 * Description: إضافة لتتبع ترتيب الكلمات المفتاحية في محركات البحث جوجل
 * Version: 1.0.0
 * Author: فوزي جمعة
 * Author URI: https://www.fjomah.com/
 * Text Domain: seo-keyword-list
 * Domain Path: /languages
 * License: GPL2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SEO_KEYWORD_LIST_VERSION', '1.0.0');
define('SEO_KEYWORD_LIST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SEO_KEYWORD_LIST_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SEO_KEYWORD_LIST_PLUGIN_DIR . 'includes/class-seo-keyword-list.php';
require_once SEO_KEYWORD_LIST_PLUGIN_DIR . 'includes/class-seo-keyword-list-admin.php';

// Initialize the plugin
function seo_keyword_list_init() {
    // Load plugin text domain
    load_plugin_textdomain('seo-keyword-list', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // Initialize the main plugin class
    $seo_keyword_list = new SEO_Keyword_List();
    $seo_keyword_list->init();
}
add_action('plugins_loaded', 'seo_keyword_list_init');

// Activation hook
register_activation_hook(__FILE__, 'seo_keyword_list_activate');
function seo_keyword_list_activate() {
    // Create database tables
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'seo_keywords';
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        keyword varchar(255) NOT NULL,
        initial_rank int(11) DEFAULT 0,
        current_rank int(11) DEFAULT 0,
        previous_update_rank int(11) DEFAULT 0,
        previous_month_rank int(11) DEFAULT 0,
        last_updated datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Add version to options
    add_option('seo_keyword_list_version', SEO_KEYWORD_LIST_VERSION);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'seo_keyword_list_deactivate');
function seo_keyword_list_deactivate() {
    // Cleanup if needed
}