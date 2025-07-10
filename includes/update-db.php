<?php
/**
 * Update database for SEO Keyword List plugin
 *
 * @package SEO_Keyword_List
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Update database to add leading_url column
 */
function seo_keyword_list_update_db() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'seo_keywords';
    
    // Check if the column already exists
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'leading_url'");
    
    if (empty($column_exists)) {
        // Add the leading_url column
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN leading_url TEXT DEFAULT NULL AFTER keyword");
        
        // Add option to indicate the database has been updated
        update_option('seo_keyword_list_db_version', '1.1');
        
        return true;
    }
    
    return false;
}