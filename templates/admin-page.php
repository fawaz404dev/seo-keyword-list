<?php
/**
 * Admin page template
 *
 * @package SEO_Keyword_List
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap seo-keyword-list-admin">
    <h1><?php echo esc_html__('ترتيب الكلمات المفتاحية في جوجل', 'seo-keyword-list'); ?></h1>
    
    <?php settings_errors('seo_keyword_list'); ?>
    
    <div class="seo-keyword-list-container">
        <div class="seo-keyword-list-add-form">
            <h2><?php echo esc_html__('إضافة كلمة مفتاحية جديدة', 'seo-keyword-list'); ?></h2>
            <form method="post" action="">
                <?php wp_nonce_field('seo_keyword_list_action', 'seo_keyword_list_nonce'); ?>
                <input type="hidden" name="seo_keyword_list_action" value="add">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="keyword"><?php echo esc_html__('الكلمة المفتاحية', 'seo-keyword-list'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="keyword" id="keyword" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="leading_url"><?php echo esc_html__('الرابط المتصدر (المقالة)', 'seo-keyword-list'); ?></label>
                        </th>
                        <td>
                            <div class="leading-url-field">
                                <input type="url" name="leading_url" id="leading_url" class="regular-text" placeholder="<?php echo esc_attr__('رابط المقالة أو الصفحة', 'seo-keyword-list'); ?>">
                            </div>
                            <p class="description"><?php echo esc_html__('الرابط المتصدر للكلمة المفتاحية في نتائج البحث.', 'seo-keyword-list'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="initial_rank"><?php echo esc_html__('الترتيب الأولي', 'seo-keyword-list'); ?></label>
                        </th>
                        <td>
                            <input type="number" name="initial_rank" id="initial_rank" class="small-text" min="0" value="0">
                            <p class="description"><?php echo esc_html__('ترتيب الكلمة المفتاحية عند بدء التتبع.', 'seo-keyword-list'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('إضافة الكلمة المفتاحية', 'seo-keyword-list')); ?>
            </form>
        </div>
        
        <div class="seo-keyword-list-table">
            <div class="seo-keyword-list-header">
                <h2><?php echo esc_html__('قائمة الكلمات المفتاحية', 'seo-keyword-list'); ?></h2>
                <div class="seo-keyword-list-actions">
                    <div class="seo-keyword-list-filter">
                        <label for="keyword-filter"><?php echo esc_html__('تصنيف حسب:', 'seo-keyword-list'); ?></label>
                        <select id="keyword-filter">
                            <option value="all"><?php echo esc_html__('الكل', 'seo-keyword-list'); ?></option>
                            <option value="positive"><?php echo esc_html__('الكلمات التي ارتفعت', 'seo-keyword-list'); ?></option>
                            <option value="negative"><?php echo esc_html__('الكلمات التي هبطت', 'seo-keyword-list'); ?></option>
                            <option value="highest"><?php echo esc_html__('الأعلى ترتيباً', 'seo-keyword-list'); ?></option>
                            <option value="alphabetical"><?php echo esc_html__('أبجدي', 'seo-keyword-list'); ?></option>
                        </select>
                    </div>
                    <button type="button" id="export-csv">
                        <?php echo esc_html__('تصدير إلى CSV', 'seo-keyword-list'); ?>
                    </button>
                </div>
            </div>
            
            <?php if (empty($keywords)) : ?>
                <p><?php echo esc_html__('لا توجد كلمات مفتاحية حتى الآن.', 'seo-keyword-list'); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('الكلمة المفتاحية', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('الرابط المتصدر', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('الترتيب الأولي', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('الترتيب الحالي', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('ترتيب التحديث السابق', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('ترتيب الشهر السابق', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('التغيير', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('آخر تحديث', 'seo-keyword-list'); ?></th>
                            <th><?php echo esc_html__('الإجراءات', 'seo-keyword-list'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keywords as $keyword) : ?>
                            <tr data-id="<?php echo esc_attr($keyword['id']); ?>">
                                <td><?php echo esc_html($keyword['keyword']); ?></td>
                                <td>
                                    <div class="leading-url-display">
                                        <?php if (!empty($keyword['leading_url'])) : ?>
                                            <a href="<?php echo esc_url($keyword['leading_url']); ?>" target="_blank" class="leading-url-link">
                                                <?php echo esc_html(wp_parse_url($keyword['leading_url'], PHP_URL_PATH)); ?>
                                            </a>
                                            <button type="button" class="copy-url-button" data-url="<?php echo esc_attr($keyword['leading_url']); ?>">
                                                <span class="dashicons dashicons-clipboard"></span>
                                            </button>
                                        <?php else : ?>
                                            <span class="no-url"><?php echo esc_html__('لا يوجد رابط', 'seo-keyword-list'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="edit-leading-url" style="display: none;">
                                        <div class="leading-url-field">
                                            <input type="url" name="leading_url" value="<?php echo esc_attr($keyword['leading_url'] ?? ''); ?>" class="regular-text" placeholder="<?php echo esc_attr__('رابط المقالة أو الصفحة', 'seo-keyword-list'); ?>">
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo esc_html($keyword['initial_rank']); ?></td>
                                <td>
                                    <span class="current-rank"><?php echo esc_html($keyword['current_rank']); ?></span>
                                    <div class="edit-rank" style="display: none;">
                                        <form method="post" action="" class="update-all-form">
                                            <?php wp_nonce_field('seo_keyword_list_action', 'seo_keyword_list_nonce'); ?>
                                            <input type="hidden" name="seo_keyword_list_action" value="update_all">
                                            <input type="hidden" name="id" value="<?php echo esc_attr($keyword['id']); ?>">
                                            <input type="number" name="current_rank" value="<?php echo esc_attr($keyword['current_rank']); ?>" min="0" class="small-text">
                                            <button type="submit" class="button button-small"><?php echo esc_html__('تحديث', 'seo-keyword-list'); ?></button>
                                            <a href="#" class="cancel-edit"><?php echo esc_html__('إلغاء', 'seo-keyword-list'); ?></a>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <span class="previous-update-rank"><?php echo esc_html($keyword['previous_update_rank']); ?></span>
                                    <div class="edit-previous-update-rank" style="display: none;">
                                        <input type="number" name="previous_update_rank" value="<?php echo esc_attr($keyword['previous_update_rank']); ?>" min="0" class="small-text">
                                    </div>
                                </td>
                                <td>
                                    <span class="previous-month-rank"><?php echo esc_html($keyword['previous_month_rank']); ?></span>
                                    <div class="edit-previous-month-rank" style="display: none;">
                                        <input type="number" name="previous_month_rank" value="<?php echo esc_attr($keyword['previous_month_rank']); ?>" min="0" class="small-text">
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    // حساب التغيير: الترتيب الأقل هو الأفضل، لذا نطرح الترتيب السابق من الترتيب الحالي
                                    // قيمة موجبة تعني تراجع في الترتيب (سلبي)، وقيمة سالبة تعني تحسن في الترتيب (إيجابي)
                                    $change = $keyword['previous_month_rank'] > 0 ? $keyword['current_rank'] - $keyword['previous_month_rank'] : 0;
                                    
                                    // تحديد نص التغيير ولونه
                                    if ($keyword['previous_month_rank'] == 0) {
                                        $change_text = __('ثابت', 'seo-keyword-list');
                                        $change_class = 'neutral';
                                        $change_icon = '-';
                                    } else if ($change < 0) {
                                        $change_text = __('ارتفع', 'seo-keyword-list');
                                        $change_class = 'positive';
                                        $change_icon = '↑';
                                    } else if ($change > 0) {
                                        $change_text = __('هبط', 'seo-keyword-list');
                                        $change_class = 'negative';
                                        $change_icon = '↓';
                                    } else {
                                        $change_text = __('ثابت', 'seo-keyword-list');
                                        $change_class = 'neutral';
                                        $change_icon = '-';
                                    }
                                    
                                    echo '<span class="change ' . esc_attr($change_class) . '">' . esc_html($change_icon . ' ' . $change_text) . '</span>';
                                    ?>
                                    <div class="edit-change" style="display: none;">
                                        <select name="change_status" class="change-status">
                                            <option value="neutral" <?php selected($change_class, 'neutral'); ?>><?php echo esc_html__('ثابت', 'seo-keyword-list'); ?></option>
                                            <option value="positive" <?php selected($change_class, 'positive'); ?>><?php echo esc_html__('ارتفع', 'seo-keyword-list'); ?></option>
                                            <option value="negative" <?php selected($change_class, 'negative'); ?>><?php echo esc_html__('هبط', 'seo-keyword-list'); ?></option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($keyword['last_updated']))); ?>
                                </td>
                                <td>
                                    <a href="#" class="edit-keyword" title="<?php echo esc_attr__('تعديل', 'seo-keyword-list'); ?>"><?php echo esc_html__('تعديل', 'seo-keyword-list'); ?></a> | 
                                    <a href="#" class="delete-keyword" title="<?php echo esc_attr__('حذف', 'seo-keyword-list'); ?>"><?php echo esc_html__('حذف', 'seo-keyword-list'); ?></a>
                                    <form method="post" action="" class="delete-form" style="display: none;">
                                        <?php wp_nonce_field('seo_keyword_list_action', 'seo_keyword_list_nonce'); ?>
                                        <input type="hidden" name="seo_keyword_list_action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo esc_attr($keyword['id']); ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- قسم حقوق التصميم والبرمجة -->
        <div class="seo-keyword-list-footer">
            <p>
                <?php echo esc_html__('تم تطوير وتصميم إضافة ترتيب الكلمات المفتاحية في جوجل بواسطة', 'seo-keyword-list'); ?>
                <a href="https://www.fjomah.com/" target="_blank">فوزي جمعة</a> &copy; <?php echo date('Y'); ?>
            </p>
        </div>
    </div>
</div>