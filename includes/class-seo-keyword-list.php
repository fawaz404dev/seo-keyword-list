<?php
/**
 * Main plugin class
 *
 * @package SEO_Keyword_List
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 */
class SEO_Keyword_List {
    
    /**
     * Plugin instance
     *
     * @var SEO_Keyword_List
     */
    private static $instance = null;
    
    /**
     * Admin class instance
     *
     * @var SEO_Keyword_List_Admin
     */
    public $admin;
    
    /**
     * Get plugin instance
     *
     * @return SEO_Keyword_List
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize admin
        if (is_admin()) {
            $this->admin = new SEO_Keyword_List_Admin();
            $this->admin->init();
            
            // Check if we need to update the database
            $this->maybe_update_db();
        }
        
        // Add hooks
        $this->add_hooks();
    }
    
    /**
     * Check if we need to update the database
     */
    private function maybe_update_db() {
        $db_version = get_option('seo_keyword_list_db_version', '1.0');
        
        if (version_compare($db_version, '1.1', '<')) {
            require_once SEO_KEYWORD_LIST_PLUGIN_DIR . 'includes/update-db.php';
            seo_keyword_list_update_db();
        }
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // Add any frontend hooks here
    }
    
    /**
     * Get keywords from database
     *
     * @return array
     */
    public function get_keywords() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_keywords';
        
        $keywords = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY keyword ASC",
            ARRAY_A
        );
        
        return $keywords;
    }
    
    /**
     * Add a new keyword
     *
     * @param string $keyword Keyword
     * @param int $initial_rank Initial rank
     * @param string $leading_url Leading URL (article)
     * @return int|false The number of rows inserted, or false on error
     */
    public function add_keyword($keyword, $initial_rank = 0, $leading_url = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_keywords';
        
        return $wpdb->insert(
            $table_name,
            array(
                'keyword' => $keyword,
                'leading_url' => $leading_url,
                'initial_rank' => $initial_rank,
                'current_rank' => $initial_rank,
                'previous_month_rank' => 0,
                'last_updated' => current_time('mysql')
            ),
            array('%s', '%s', '%d', '%d', '%d', '%s')
        );
    }
    
    /**
     * Update keyword rank
     *
     * @param int $id Keyword ID
     * @param int $current_rank Current rank
     * @return int|false The number of rows updated, or false on error
     */
    public function update_keyword_rank($id, $current_rank) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_keywords';
        
        return $wpdb->update(
            $table_name,
            array(
                'current_rank' => $current_rank,
                'last_updated' => current_time('mysql')
            ),
            array('id' => $id),
            array('%d', '%s'),
            array('%d')
        );
    }
    
    /**
     * Delete keyword
     *
     * @param int $id Keyword ID
     * @return int|false The number of rows deleted, or false on error
     */
    public function delete_keyword($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_keywords';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Update previous month ranks
     * This should be called monthly via cron
     */
    public function update_previous_month_ranks() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_keywords';
        
        $wpdb->query("UPDATE $table_name SET previous_month_rank = current_rank");
    }
    
    /**
     * Update keyword all ranks
     *
     * @param int $id Keyword ID
     * @param int $current_rank Current rank
     * @param int $previous_update_rank Previous update rank
     * @param int $previous_month_rank Previous month rank
     * @param string $change_status Change status (positive, negative, neutral)
     * @param string $leading_url Leading URL (article)
     * @return int|false The number of rows updated, or false on error
     */
    public function update_keyword_all_ranks($id, $current_rank, $previous_update_rank, $previous_month_rank, $change_status = 'neutral', $leading_url = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_keywords';
        
        $data = array(
            'current_rank' => $current_rank,
            'previous_update_rank' => $previous_update_rank,
            'previous_month_rank' => $previous_month_rank,
            'last_updated' => current_time('mysql')
        );
        
        $formats = array('%d', '%d', '%d', '%s');
        
        // Only update leading_url if it's provided
        if ($leading_url !== null) {
            $data['leading_url'] = $leading_url;
            $formats[] = '%s';
        }
        
        return $wpdb->update(
            $table_name,
            $data,
            array('id' => $id),
            $formats,
            array('%d')
        );
    }
}