<?php
/**
 * Admin class
 *
 * @package SEO_Keyword_List
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class
 */
class SEO_Keyword_List_Admin {
    
    /**
     * Initialize admin
     */
    public function init() {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add AJAX handlers
        add_action('wp_ajax_seo_keyword_list_add', array($this, 'ajax_add_keyword'));
        add_action('wp_ajax_seo_keyword_list_update', array($this, 'ajax_update_keyword'));
        add_action('wp_ajax_seo_keyword_list_update_all', array($this, 'ajax_update_all_ranks'));
        add_action('wp_ajax_seo_keyword_list_delete', array($this, 'ajax_delete_keyword'));
        add_action('wp_ajax_seo_keyword_list_export_csv', array($this, 'ajax_export_csv'));
        
        // Add monthly cron job to update previous month ranks
        if (!wp_next_scheduled('seo_keyword_list_monthly_update')) {
            wp_schedule_event(time(), 'monthly', 'seo_keyword_list_monthly_update');
        }
        add_action('seo_keyword_list_monthly_update', array($this, 'monthly_update'));
        
        // Handle export action
        add_action('admin_init', array($this, 'handle_export_action'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('ترتيب الكلمات المفتاحية', 'seo-keyword-list'),
            __('الكلمات المفتاحية', 'seo-keyword-list'),
            'manage_options',
            'seo-keyword-list',
            array($this, 'display_admin_page'),
            'dashicons-chart-line',
            30
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Hook suffix
     */
    public function enqueue_scripts($hook) {
        if ('toplevel_page_seo-keyword-list' !== $hook) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'seo-keyword-list-admin',
            SEO_KEYWORD_LIST_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SEO_KEYWORD_LIST_VERSION
        );
        
        // تم إزالة إضافة وسائط WordPress لأننا لم نعد نستخدم زر اختيار المقالة
        
        // Enqueue scripts
        wp_enqueue_script(
            'seo-keyword-list-admin',
            SEO_KEYWORD_LIST_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            SEO_KEYWORD_LIST_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script(
            'seo-keyword-list-admin',
            'seo_keyword_list',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('seo_keyword_list_nonce'),
                'confirm_delete' => __('هل أنت متأكد من رغبتك في حذف هذه الكلمة المفتاحية؟', 'seo-keyword-list'),
                'error_message' => __('حدث خطأ. يرجى المحاولة مرة أخرى.', 'seo-keyword-list'),
                'export_preparing' => __('جاري تحضير ملف التصدير...', 'seo-keyword-list'),
                'export_success' => __('تم تصدير الكلمات المفتاحية بنجاح.', 'seo-keyword-list'),
                'export_error' => __('حدث خطأ أثناء تصدير الكلمات المفتاحية.', 'seo-keyword-list'),

                'copy_url' => __('نسخ الرابط', 'seo-keyword-list'),
                'url_copied' => __('تم نسخ الرابط!', 'seo-keyword-list')
            )
        );
    }
    
    /**
     * Display admin page
     */
    public function display_admin_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Process form submission
        $this->process_form();
        
        // Get keywords
        $keywords = SEO_Keyword_List::get_instance()->get_keywords();
        
        // Include template
        include SEO_KEYWORD_LIST_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    /**
     * Process form submission
     */
    private function process_form() {
        // Check if form was submitted
        if (!isset($_POST['seo_keyword_list_action'])) {
            return;
        }
        
        // Verify nonce
        if (!isset($_POST['seo_keyword_list_nonce']) || !wp_verify_nonce($_POST['seo_keyword_list_nonce'], 'seo_keyword_list_action')) {
            add_settings_error('seo_keyword_list', 'invalid_nonce', __('خطأ في التحقق من الأمان.', 'seo-keyword-list'), 'error');
            return;
        }
        
        $action = sanitize_text_field($_POST['seo_keyword_list_action']);
        
        switch ($action) {
            case 'add':
                $this->process_add_keyword();
                break;
                
            case 'update':
                $this->process_update_keyword();
                break;
                
            case 'delete':
                $this->process_delete_keyword();
                break;
        }
    }
    
    /**
     * Process add keyword form
     */
    private function process_add_keyword() {
        // Validate input
        if (!isset($_POST['keyword']) || empty($_POST['keyword'])) {
            add_settings_error('seo_keyword_list', 'empty_keyword', __('يرجى إدخال كلمة مفتاحية.', 'seo-keyword-list'), 'error');
            return;
        }
        
        $keyword = sanitize_text_field($_POST['keyword']);
        $initial_rank = isset($_POST['initial_rank']) ? intval($_POST['initial_rank']) : 0;
        $leading_url = isset($_POST['leading_url']) ? esc_url_raw($_POST['leading_url']) : '';
        
        // Add keyword
        $result = SEO_Keyword_List::get_instance()->add_keyword($keyword, $initial_rank, $leading_url);
        
        if ($result) {
            add_settings_error('seo_keyword_list', 'keyword_added', __('تمت إضافة الكلمة المفتاحية بنجاح.', 'seo-keyword-list'), 'success');
        } else {
            add_settings_error('seo_keyword_list', 'add_failed', __('فشل في إضافة الكلمة المفتاحية.', 'seo-keyword-list'), 'error');
        }
    }
    
    /**
     * Process update keyword form
     */
    private function process_update_keyword() {
        // Validate input
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            add_settings_error('seo_keyword_list', 'invalid_id', __('معرف الكلمة المفتاحية غير صالح.', 'seo-keyword-list'), 'error');
            return;
        }
        
        $id = intval($_POST['id']);
        $current_rank = isset($_POST['current_rank']) ? intval($_POST['current_rank']) : 0;
        
        // Update keyword
        $result = SEO_Keyword_List::get_instance()->update_keyword_rank($id, $current_rank);
        
        if ($result) {
            add_settings_error('seo_keyword_list', 'keyword_updated', __('تم تحديث الكلمة المفتاحية بنجاح.', 'seo-keyword-list'), 'success');
        } else {
            add_settings_error('seo_keyword_list', 'update_failed', __('فشل في تحديث الكلمة المفتاحية.', 'seo-keyword-list'), 'error');
        }
    }
    
    /**
     * Process delete keyword form
     */
    private function process_delete_keyword() {
        // Validate input
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            add_settings_error('seo_keyword_list', 'invalid_id', __('معرف الكلمة المفتاحية غير صالح.', 'seo-keyword-list'), 'error');
            return;
        }
        
        $id = intval($_POST['id']);
        
        // Delete keyword
        $result = SEO_Keyword_List::get_instance()->delete_keyword($id);
        
        if ($result) {
            add_settings_error('seo_keyword_list', 'keyword_deleted', __('تم حذف الكلمة المفتاحية بنجاح.', 'seo-keyword-list'), 'success');
        } else {
            add_settings_error('seo_keyword_list', 'delete_failed', __('فشل في حذف الكلمة المفتاحية.', 'seo-keyword-list'), 'error');
        }
    }
    
    /**
     * AJAX add keyword
     */
    public function ajax_add_keyword() {
        global $wpdb;
        
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'seo_keyword_list_nonce')) {
            wp_send_json_error(array('message' => __('خطأ في التحقق من الأمان.', 'seo-keyword-list')));
        }
        
        // Validate input
        if (!isset($_POST['keyword']) || empty($_POST['keyword'])) {
            wp_send_json_error(array('message' => __('يرجى إدخال كلمة مفتاحية.', 'seo-keyword-list')));
        }
        
        $keyword = sanitize_text_field($_POST['keyword']);
        $initial_rank = isset($_POST['initial_rank']) ? intval($_POST['initial_rank']) : 0;
        $leading_url = isset($_POST['leading_url']) ? esc_url_raw($_POST['leading_url']) : '';
        
        // Add keyword
        $result = SEO_Keyword_List::get_instance()->add_keyword($keyword, $initial_rank, $leading_url);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('تمت إضافة الكلمة المفتاحية بنجاح.', 'seo-keyword-list'),
                'keyword' => array(
                    'id' => $wpdb->insert_id,
                    'keyword' => $keyword,
                    'leading_url' => $leading_url,
                    'initial_rank' => $initial_rank,
                    'current_rank' => $initial_rank,
                    'previous_month_rank' => 0,
                    'last_updated' => current_time('mysql')
                )
            ));
        } else {
            wp_send_json_error(array('message' => __('فشل في إضافة الكلمة المفتاحية.', 'seo-keyword-list')));
        }
    }
    
    /**
     * AJAX update keyword
     */
    public function ajax_update_keyword() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'seo_keyword_list_nonce')) {
            wp_send_json_error(array('message' => __('خطأ في التحقق من الأمان.', 'seo-keyword-list')));
        }
        
        // Validate input
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => __('معرف الكلمة المفتاحية غير صالح.', 'seo-keyword-list')));
        }
        
        $id = intval($_POST['id']);
        $current_rank = isset($_POST['current_rank']) ? intval($_POST['current_rank']) : 0;
        
        // Update keyword
        $result = SEO_Keyword_List::get_instance()->update_keyword_rank($id, $current_rank);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('تم تحديث الكلمة المفتاحية بنجاح.', 'seo-keyword-list'),
                'id' => $id,
                'current_rank' => $current_rank,
                'last_updated' => current_time('mysql')
            ));
        } else {
            wp_send_json_error(array('message' => __('فشل في تحديث الكلمة المفتاحية.', 'seo-keyword-list')));
        }
    }
    
    /**
     * AJAX update all ranks
     */
    public function ajax_update_all_ranks() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'seo_keyword_list_nonce')) {
            wp_send_json_error(array('message' => __('خطأ في التحقق من الأمان.', 'seo-keyword-list')));
        }
        
        // Validate input
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => __('معرف الكلمة المفتاحية غير صالح.', 'seo-keyword-list')));
        }
        
        $id = intval($_POST['id']);
        $current_rank = isset($_POST['current_rank']) ? intval($_POST['current_rank']) : 0;
        $previous_update_rank = isset($_POST['previous_update_rank']) ? intval($_POST['previous_update_rank']) : 0;
        $previous_month_rank = isset($_POST['previous_month_rank']) ? intval($_POST['previous_month_rank']) : 0;
        $change_status = isset($_POST['change_status']) ? sanitize_text_field($_POST['change_status']) : 'neutral';
        $leading_url = isset($_POST['leading_url']) ? esc_url_raw($_POST['leading_url']) : null;
        
        // Update keyword all ranks
        $result = SEO_Keyword_List::get_instance()->update_keyword_all_ranks(
            $id, 
            $current_rank, 
            $previous_update_rank, 
            $previous_month_rank, 
            $change_status,
            $leading_url
        );
        
        if ($result) {
            // حساب التغيير بناءً على حالة التغيير المحددة
            $change_text = __('ثابت', 'seo-keyword-list');
            $change_class = 'neutral';
            $change_icon = '-';
            
            if ($change_status === 'positive') {
                $change_text = __('ارتفع', 'seo-keyword-list');
                $change_class = 'positive';
                $change_icon = '↑';
            } else if ($change_status === 'negative') {
                $change_text = __('هبط', 'seo-keyword-list');
                $change_class = 'negative';
                $change_icon = '↓';
            }
            
            $response_data = array(
                'message' => __('تم تحديث الكلمة المفتاحية بنجاح.', 'seo-keyword-list'),
                'id' => $id,
                'current_rank' => $current_rank,
                'previous_update_rank' => $previous_update_rank,
                'previous_month_rank' => $previous_month_rank,
                'change_status' => $change_status,
                'change_text' => $change_text,
                'change_class' => $change_class,
                'change_icon' => $change_icon,
                'last_updated' => current_time('mysql')
            );
            
            if ($leading_url !== null) {
                $response_data['leading_url'] = $leading_url;
            }
            
            wp_send_json_success($response_data);
        } else {
            wp_send_json_error(array('message' => __('فشل في تحديث الكلمة المفتاحية.', 'seo-keyword-list')));
        }
    }
    
    /**
     * AJAX delete keyword
     */
    public function ajax_delete_keyword() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'seo_keyword_list_nonce')) {
            wp_send_json_error(array('message' => __('خطأ في التحقق من الأمان.', 'seo-keyword-list')));
        }
        
        // Validate input
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => __('معرف الكلمة المفتاحية غير صالح.', 'seo-keyword-list')));
        }
        
        $id = intval($_POST['id']);
        
        // Delete keyword
        $result = SEO_Keyword_List::get_instance()->delete_keyword($id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('تم حذف الكلمة المفتاحية بنجاح.', 'seo-keyword-list'),
                'id' => $id
            ));
        } else {
            wp_send_json_error(array('message' => __('فشل في حذف الكلمة المفتاحية.', 'seo-keyword-list')));
        }
    }
    
    /**
     * Monthly update
     * Update previous month ranks
     */
    public function monthly_update() {
        SEO_Keyword_List::get_instance()->update_previous_month_ranks();
    }
    
    /**
     * Handle export action
     */
    public function handle_export_action() {
        if (isset($_GET['action']) && $_GET['action'] === 'export_csv' && 
            isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'seo_keyword_list_export')) {
            
            $this->export_csv();
            exit;
        }
    }
    
    /**
     * Export keywords to CSV
     */
    private function export_csv() {
        $keywords = SEO_Keyword_List::get_instance()->get_keywords();
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=seo-keywords-' . date('Y-m-d') . '.csv');
        
        // Create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM
        fprintf($output, "\xEF\xBB\xBF");
        
        // Add CSV headers
        fputcsv($output, array(
            __('الكلمة المفتاحية', 'seo-keyword-list'),
            __('الترتيب الأولي', 'seo-keyword-list'),
            __('الترتيب الحالي', 'seo-keyword-list'),
            __('ترتيب التحديث السابق', 'seo-keyword-list'),
            __('ترتيب الشهر السابق', 'seo-keyword-list'),
            __('التغيير', 'seo-keyword-list'),
            __('آخر تحديث', 'seo-keyword-list')
        ));
        
        // Add data rows
        foreach ($keywords as $keyword) {
            $change_value = $keyword->previous_month_rank > 0 ? $keyword->current_rank - $keyword->previous_month_rank : 0;
            
            // تحويل التغيير إلى نص وصفي
            $change_text = 'ثابت';
            if ($change_value < 0) {
                $change_text = 'ارتفع';
            } elseif ($change_value > 0) {
                $change_text = 'هبط';
            }
            
            fputcsv($output, array(
                $keyword->keyword,
                $keyword->initial_rank,
                $keyword->current_rank,
                $keyword->previous_update_rank,
                $keyword->previous_month_rank,
                $change_text,
                $keyword->last_updated
            ));
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * AJAX export CSV
     */
    public function ajax_export_csv() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'seo_keyword_list_nonce')) {
            wp_send_json_error(array('message' => __('خطأ في التحقق من الأمان.', 'seo-keyword-list')));
        }
        
        // Generate export URL with nonce
        $export_url = add_query_arg(
            array(
                'action' => 'export_csv',
                '_wpnonce' => wp_create_nonce('seo_keyword_list_export')
            ),
            admin_url('admin.php?page=seo-keyword-list')
        );
        
        wp_send_json_success(array(
            'message' => __('جاري تحضير ملف التصدير...', 'seo-keyword-list'),
            'export_url' => $export_url
        ));
    }
}