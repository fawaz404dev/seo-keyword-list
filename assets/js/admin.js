/**
 * Admin JavaScript for SEO Keyword List plugin
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // نسخ الرابط المتصدر
        $('.seo-keyword-list-container').on('click', '.copy-url-button', function(e) {
            e.preventDefault();
            var button = $(this);
            var url = button.data('url');
            
            // نسخ الرابط إلى الحافظة
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(url).select();
            document.execCommand('copy');
            tempInput.remove();
            
            // إظهار رسالة نجاح
            var originalIcon = button.find('span').attr('class');
            button.find('span').removeClass().addClass('dashicons dashicons-yes');
            
            // إعادة الأيقونة الأصلية بعد ثانيتين
            setTimeout(function() {
                button.find('span').removeClass().addClass(originalIcon);
            }, 2000);
            
            // إظهار رسالة
            showMessage(seo_keyword_list.url_copied, 'success');
        });
        
        // إظهار/إخفاء حقل تعديل الرابط المتصدر عند التعديل
        $('.seo-keyword-list-table').on('click', '.edit-keyword', function(e) {
            var row = $(this).closest('tr');
            row.find('.leading-url-display').hide();
            row.find('.edit-leading-url').show();
        });
        
        // إخفاء حقل تعديل الرابط المتصدر عند الإلغاء
        $('.seo-keyword-list-table').on('click', '.cancel-edit', function() {
            var row = $(this).closest('tr');
            row.find('.edit-leading-url').hide();
            row.find('.leading-url-display').show();
        });
        // تصنيف الكلمات المفتاحية
        $('#keyword-filter').on('change', function() {
            var filterValue = $(this).val();
            var rows = $('.seo-keyword-list-table table tbody tr').get();
            
            if (filterValue === 'all') {
                // إظهار جميع الصفوف
                $('.seo-keyword-list-table table tbody tr').show();
                return;
            }
            
            // إخفاء جميع الصفوف أولاً
            $('.seo-keyword-list-table table tbody tr').hide();
            
            if (filterValue === 'positive') {
                // إظهار الكلمات التي ارتفعت فقط
                $('.seo-keyword-list-table table tbody tr').each(function() {
                    if ($(this).find('td:nth-child(6) .change').hasClass('positive')) {
                        $(this).show();
                    }
                });
            } else if (filterValue === 'negative') {
                // إظهار الكلمات التي هبطت فقط
                $('.seo-keyword-list-table table tbody tr').each(function() {
                    if ($(this).find('td:nth-child(6) .change').hasClass('negative')) {
                        $(this).show();
                    }
                });
            } else if (filterValue === 'highest') {
                // ترتيب حسب الترتيب الحالي (الأقل هو الأعلى)
                rows.sort(function(a, b) {
                    var rankA = parseInt($(a).find('td:nth-child(3) .current-rank').text());
                    var rankB = parseInt($(b).find('td:nth-child(3) .current-rank').text());
                    return rankA - rankB;
                });
                
                $.each(rows, function(index, row) {
                    $('.seo-keyword-list-table table tbody').append(row);
                    $(row).show();
                });
            } else if (filterValue === 'alphabetical') {
                // ترتيب أبجدي حسب اسم الكلمة المفتاحية
                rows.sort(function(a, b) {
                    var keywordA = $(a).find('td:nth-child(1)').text().toLowerCase();
                    var keywordB = $(b).find('td:nth-child(1)').text().toLowerCase();
                    if (keywordA < keywordB) return -1;
                    if (keywordA > keywordB) return 1;
                    return 0;
                });
                
                $.each(rows, function(index, row) {
                    $('.seo-keyword-list-table table tbody').append(row);
                    $(row).show();
                });
            }
        });
        
        // Edit keyword rank
        $('.seo-keyword-list-table').on('click', '.edit-keyword', function(e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            
            // إخفاء جميع القيم وإظهار حقول التعديل
            row.find('.current-rank').hide();
            row.find('.edit-rank').show();
            
            row.find('.previous-update-rank').hide();
            row.find('.edit-previous-update-rank').show();
            
            row.find('.previous-month-rank').hide();
            row.find('.edit-previous-month-rank').show();
            
            row.find('.change').hide();
            row.find('.edit-change').show();
        });
        
        // Cancel edit
        $('.seo-keyword-list-table').on('click', '.cancel-edit', function() {
            var row = $(this).closest('tr');
            
            // إخفاء حقول التعديل وإظهار جميع القيم
            row.find('.edit-rank').hide();
            row.find('.current-rank').show();
            
            row.find('.edit-previous-update-rank').hide();
            row.find('.previous-update-rank').show();
            
            row.find('.edit-previous-month-rank').hide();
            row.find('.previous-month-rank').show();
            
            row.find('.edit-change').hide();
            row.find('.change').show();
        });
        
        // Delete keyword
        $('.seo-keyword-list-table').on('click', '.delete-keyword', function(e) {
            e.preventDefault();
            if (confirm(seo_keyword_list.confirm_delete)) {
                $(this).siblings('.delete-form').submit();
            }
        });
        
        // AJAX form submissions
        if (typeof seo_keyword_list !== 'undefined') {
            // Add keyword form
            $('#seo-keyword-add-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var keyword = form.find('#keyword').val();
                var initialRank = form.find('#initial_rank').val();
                var leadingUrl = form.find('#leading_url').val();
                
                $.ajax({
                    url: seo_keyword_list.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'seo_keyword_list_add',
                        nonce: seo_keyword_list.nonce,
                        keyword: keyword,
                        initial_rank: initialRank,
                        leading_url: leadingUrl
                    },
                    beforeSend: function() {
                        form.find('button').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showMessage(response.data.message, 'success');
                            
                            // Reset form
                            form.find('#keyword').val('');
                            form.find('#initial_rank').val('0');
                            form.find('#leading_url').val('');
                            
                            // Reload page to show new keyword
                            location.reload();
                        } else {
                            showMessage(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        showMessage(seo_keyword_list.error_message, 'error');
                    },
                    complete: function() {
                        form.find('button').prop('disabled', false);
                    }
                });
            });
            
            // Update keyword form
            $('.seo-keyword-list-table').on('submit', '.update-form', function(e) {
                e.preventDefault();
                var form = $(this);
                var row = form.closest('tr');
                var id = row.data('id');
                var currentRank = form.find('input[name="current_rank"]').val();
                
                $.ajax({
                    url: seo_keyword_list.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'seo_keyword_list_update',
                        nonce: seo_keyword_list.nonce,
                        id: id,
                        current_rank: currentRank
                    },
                    beforeSend: function() {
                        form.find('button').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showMessage(response.data.message, 'success');
                            
                            // الحصول على الترتيب الحالي قبل التحديث
                            var oldRank = row.find('.current-rank').text();
                            
                            // تحديث عرض الترتيب الحالي
                            row.find('.current-rank').text(currentRank);
                            row.find('.edit-rank').hide();
                            row.find('.current-rank').show();
                            
                            // تحديث ترتيب التحديث السابق
                            row.find('td:nth-child(4)').text(oldRank);
                            
                            // تحديث وقت آخر تحديث
                            var now = new Date();
                            var formattedDate = now.toLocaleString();
                            row.find('td:nth-child(8)').text(formattedDate);
                            
                            // تحديث التغيير إذا كان هناك ترتيب للشهر السابق
                            var previousMonthRank = parseInt(row.find('td:nth-child(5)').text());
                            if (previousMonthRank > 0) {
                                var change = parseInt(currentRank) - previousMonthRank;
                                var changeClass = change < 0 ? 'positive' : (change > 0 ? 'negative' : 'neutral');
                                var changeIcon = change < 0 ? '↑' : (change > 0 ? '↓' : '-');
                                var changeText = change === 0 ? 'ثابت' : (change < 0 ? 'ارتفع' : 'هبط');
                                var changeHtml = '<span class="change ' + changeClass + '">' + changeIcon + ' ' + changeText + '</span>';
                                row.find('td:nth-child(6)').html(changeHtml);
                            }
                        } else {
                            showMessage(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        showMessage(seo_keyword_list.error_message, 'error');
                    },
                    complete: function() {
                        form.find('button').prop('disabled', false);
                    }
                });
            });
            
            // Update all ranks form
            $('.seo-keyword-list-table').on('submit', '.update-all-form', function(e) {
                e.preventDefault();
                var form = $(this);
                var row = form.closest('tr');
                var id = row.data('id');
                var currentRank = form.find('input[name="current_rank"]').val();
                
                // الحصول على قيمة ترتيب التحديث السابق من الخلية المناسبة
                var previousUpdateRank = row.find('td:nth-child(4) .previous-update-rank').text().trim();
                if (row.find('td:nth-child(4) .edit-previous-update-rank input').length) {
                    previousUpdateRank = row.find('td:nth-child(4) .edit-previous-update-rank input').val();
                }
                
                // الحصول على قيمة ترتيب الشهر السابق من الخلية المناسبة
                var previousMonthRank = row.find('td:nth-child(5) .previous-month-rank').text().trim();
                if (row.find('td:nth-child(5) .edit-previous-month-rank input').length) {
                    previousMonthRank = row.find('td:nth-child(5) .edit-previous-month-rank input').val();
                }
                
                // الحصول على حالة التغيير من الخلية المناسبة
                var changeStatus = 'neutral';
                if (row.find('td:nth-child(7) .change').hasClass('positive')) {
                    changeStatus = 'positive';
                } else if (row.find('td:nth-child(7) .change').hasClass('negative')) {
                    changeStatus = 'negative';
                }
                if (row.find('td:nth-child(7) .edit-change select').length) {
                    changeStatus = row.find('td:nth-child(7) .edit-change select').val();
                }
                
                // الحصول على الرابط المتصدر
                var leadingUrl = '';
                if (row.find('.edit-leading-url input[name="leading_url"]').length) {
                    leadingUrl = row.find('.edit-leading-url input[name="leading_url"]').val();
                }
                
                $.ajax({
                    url: seo_keyword_list.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'seo_keyword_list_update_all',
                        nonce: seo_keyword_list.nonce,
                        id: id,
                        current_rank: currentRank,
                        previous_update_rank: previousUpdateRank,
                        previous_month_rank: previousMonthRank,
                        change_status: changeStatus,
                        leading_url: leadingUrl
                    },
                    beforeSend: function() {
                        form.find('button').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showMessage(response.data.message, 'success');
                            
                            // تحديث عرض جميع القيم
                            row.find('.current-rank').text(response.data.current_rank);
                            row.find('.previous-update-rank').text(response.data.previous_update_rank);
                            row.find('.previous-month-rank').text(response.data.previous_month_rank);
                            
                            // تحديث التغيير
                            var changeHtml = '<span class="change ' + response.data.change_class + '">' + 
                                             response.data.change_icon + ' ' + response.data.change_text + '</span>';
                            row.find('td:nth-child(7) .change').replaceWith(changeHtml);
                            
                            // تحديث الرابط المتصدر إذا تم تغييره
                            if (response.data.leading_url !== undefined) {
                                var leadingUrl = response.data.leading_url;
                                var urlPath = leadingUrl ? new URL(leadingUrl).pathname : '';
                                
                                if (leadingUrl) {
                                    var urlHtml = '<a href="' + leadingUrl + '" target="_blank" class="leading-url-link">' + 
                                                 urlPath + '</a>' +
                                                 '<button type="button" class="copy-url-button" data-url="' + leadingUrl + '">' +
                                                 '<span class="dashicons dashicons-clipboard"></span></button>';
                                    row.find('.leading-url-display').html(urlHtml);
                                } else {
                                    row.find('.leading-url-display').html('<span class="no-url">لا يوجد رابط</span>');
                                }
                            }
                            
                            // تحديث وقت آخر تحديث
                            var now = new Date();
                            var formattedDate = now.toLocaleString();
                            row.find('td:nth-child(7)').text(formattedDate);
                            
                            // إخفاء حقول التعديل
                            row.find('.edit-rank').hide();
                            row.find('.current-rank').show();
                            row.find('.edit-previous-update-rank').hide();
                            row.find('.previous-update-rank').show();
                            row.find('.edit-previous-month-rank').hide();
                            row.find('.previous-month-rank').show();
                            row.find('.edit-change').hide();
                            row.find('.change').show();
                            row.find('.edit-leading-url').hide();
                            row.find('.leading-url-display').show();
                        } else {
                            showMessage(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        showMessage(seo_keyword_list.error_message, 'error');
                    },
                    complete: function() {
                        form.find('button').prop('disabled', false);
                    }
                });
            });
        }
        
        // Export to CSV
        $('#export-csv').on('click', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: seo_keyword_list.ajax_url,
                type: 'POST',
                data: {
                    action: 'seo_keyword_list_export_csv',
                    nonce: seo_keyword_list.nonce
                },
                beforeSend: function() {
                    $('#export-csv').prop('disabled', true);
                    showMessage(seo_keyword_list.export_preparing, 'info');
                },
                success: function(response) {
                    if (response.success) {
                        // Redirect to export URL
                        window.location.href = response.data.export_url;
                    } else {
                        showMessage(response.data.message, 'error');
                    }
                },
                error: function() {
                    showMessage(seo_keyword_list.error_message, 'error');
                },
                complete: function() {
                    $('#export-csv').prop('disabled', false);
                }
            });
        });
        
        // Helper function to show messages
        function showMessage(message, type) {
            var messageHtml = '<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>';
            $('.seo-keyword-list-admin h1').after(messageHtml);
            
            // Auto dismiss after 3 seconds
            setTimeout(function() {
                $('.notice').fadeOut();
            }, 3000);
        }
    });
})(jQuery);