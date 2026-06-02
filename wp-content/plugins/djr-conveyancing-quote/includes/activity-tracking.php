<?php
if (!defined('ABSPATH')) exit;

function osl_cq_activity_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'osl_cq_quote_events';
}

function osl_cq_activity_allowed_events() {
    return array(
        'quote_started',
        'quote_generated',
        'quote_contact_clicked',
        'quote_email_clicked',
        'quote_phone_clicked',
        'quote_download_clicked',
        'quote_page_cta_clicked',
    );
}

function osl_cq_activity_retention_options() {
    return array(
        'forever' => 'Keep forever',
        '90' => '90 days',
        '180' => '180 days',
        '365' => '365 days',
    );
}

function osl_cq_get_activity_retention() {
    $value = get_option('osl_cq_quote_activity_retention_days', 'forever');
    return array_key_exists($value, osl_cq_activity_retention_options()) ? $value : 'forever';
}

function osl_cq_create_activity_table() {
    global $wpdb;

    $table_name = osl_cq_activity_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql = "CREATE TABLE {$table_name} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        created_at datetime NOT NULL,
        event_name varchar(64) NOT NULL,
        event_source varchar(32) NULL,
        quote_token varchar(80) NULL,
        page_url text NULL,
        page_path varchar(255) NULL,
        transaction_type varchar(32) NULL,
        property_type varchar(64) NULL,
        council varchar(120) NULL,
        suburb varchar(120) NULL,
        quote_total decimal(10,2) NULL,
        quote_total_band varchar(32) NULL,
        email varchar(190) NULL,
        phone varchar(64) NULL,
        user_agent_hash char(64) NULL,
        ip_hash char(64) NULL,
        utm_source varchar(120) NULL,
        utm_medium varchar(120) NULL,
        utm_campaign varchar(190) NULL,
        utm_term varchar(190) NULL,
        utm_content varchar(190) NULL,
        gclid varchar(190) NULL,
        fbclid varchar(190) NULL,
        extra longtext NULL,
        PRIMARY KEY  (id),
        KEY event_name (event_name),
        KEY quote_token (quote_token),
        KEY created_at (created_at)
    ) {$charset_collate};";

    dbDelta($sql);
    update_option('osl_cq_activity_table_version', '1.0.0', false);
}

function osl_cq_activity_table_exists() {
    global $wpdb;
    $table_name = osl_cq_activity_table_name();
    return $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name)) === $table_name;
}

function osl_cq_maybe_create_activity_table() {
    if (get_option('osl_cq_activity_table_version') !== '1.0.0' || !osl_cq_activity_table_exists()) {
        osl_cq_create_activity_table();
    }
}
add_action('admin_init', 'osl_cq_maybe_create_activity_table');

function osl_cq_activity_quote_band($total) {
    if ($total === null || $total === '') return '';
    $total = floatval($total);
    if ($total < 1000) return 'under_1000';
    if ($total < 1500) return '1000_1499';
    if ($total < 2000) return '1500_1999';
    if ($total < 2500) return '2000_2499';
    return '2500_plus';
}

function osl_cq_activity_page_path($value) {
    $value = sanitize_text_field((string) $value);
    if ($value === '') return '';
    return wp_parse_url($value, PHP_URL_PATH) ?: $value;
}

function osl_cq_activity_sanitize_event($event_name) {
    $event_name = sanitize_key($event_name);
    return in_array($event_name, osl_cq_activity_allowed_events(), true) ? $event_name : '';
}

function osl_cq_collect_activity_payload($source = null) {
    $source = is_array($source) ? $source : $_POST;

    $page_url = isset($source['page_url']) ? esc_url_raw(wp_unslash($source['page_url'])) : '';
    $referer = wp_get_referer();

    if ($page_url === '' && $referer) {
        $page_url = esc_url_raw($referer);
    }

    $page_path = isset($source['page_path']) ? osl_cq_activity_page_path(wp_unslash($source['page_path'])) : '';
    if ($page_path === '' && $page_url !== '') {
        $page_path = osl_cq_activity_page_path($page_url);
    }

    return array(
        'quote_token' => isset($source['quote_token']) ? sanitize_text_field(wp_unslash($source['quote_token'])) : '',
        'page_url' => $page_url,
        'page_path' => $page_path,
        'suburb' => isset($source['suburb']) ? sanitize_text_field(wp_unslash($source['suburb'])) : '',
        'utm_source' => isset($source['utm_source']) ? sanitize_text_field(wp_unslash($source['utm_source'])) : '',
        'utm_medium' => isset($source['utm_medium']) ? sanitize_text_field(wp_unslash($source['utm_medium'])) : '',
        'utm_campaign' => isset($source['utm_campaign']) ? sanitize_text_field(wp_unslash($source['utm_campaign'])) : '',
        'utm_term' => isset($source['utm_term']) ? sanitize_text_field(wp_unslash($source['utm_term'])) : '',
        'utm_content' => isset($source['utm_content']) ? sanitize_text_field(wp_unslash($source['utm_content'])) : '',
        'gclid' => isset($source['gclid']) ? sanitize_text_field(wp_unslash($source['gclid'])) : '',
        'fbclid' => isset($source['fbclid']) ? sanitize_text_field(wp_unslash($source['fbclid'])) : '',
    );
}

function osl_cq_log_activity($event_name, $data = array(), $source = 'server') {
    global $wpdb;

    $event_name = osl_cq_activity_sanitize_event($event_name);
    if ($event_name === '') {
        return false;
    }

    if (!osl_cq_activity_table_exists()) {
        osl_cq_create_activity_table();
    }

    $quote_total = array_key_exists('quote_total', $data) && $data['quote_total'] !== '' ? round(floatval($data['quote_total']), 2) : null;
    $quote_total_band = !empty($data['quote_total_band']) ? sanitize_text_field($data['quote_total_band']) : osl_cq_activity_quote_band($quote_total);
    $email = !empty($data['email']) ? sanitize_email($data['email']) : '';
    $phone = !empty($data['phone']) ? sanitize_text_field($data['phone']) : '';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])), 0, 500) : '';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    $hash_salt = wp_salt('auth');
    $extra = !empty($data['extra']) && is_array($data['extra']) ? wp_json_encode(array_map('sanitize_text_field', $data['extra'])) : null;

    $inserted = $wpdb->insert(
        osl_cq_activity_table_name(),
        array(
            'created_at' => current_time('mysql'),
            'event_name' => $event_name,
            'event_source' => sanitize_key($source),
            'quote_token' => !empty($data['quote_token']) ? sanitize_text_field($data['quote_token']) : '',
            'page_url' => !empty($data['page_url']) ? esc_url_raw($data['page_url']) : '',
            'page_path' => !empty($data['page_path']) ? osl_cq_activity_page_path($data['page_path']) : '',
            'transaction_type' => !empty($data['transaction_type']) ? sanitize_text_field($data['transaction_type']) : '',
            'property_type' => !empty($data['property_type']) ? sanitize_text_field($data['property_type']) : '',
            'council' => !empty($data['council']) ? sanitize_text_field($data['council']) : '',
            'suburb' => !empty($data['suburb']) ? sanitize_text_field($data['suburb']) : '',
            'quote_total' => $quote_total,
            'quote_total_band' => $quote_total_band,
            'email' => is_email($email) ? $email : '',
            'phone' => $phone,
            'user_agent_hash' => $user_agent ? hash_hmac('sha256', $user_agent, $hash_salt) : '',
            'ip_hash' => $ip ? hash_hmac('sha256', $ip, $hash_salt) : '',
            'utm_source' => !empty($data['utm_source']) ? sanitize_text_field($data['utm_source']) : '',
            'utm_medium' => !empty($data['utm_medium']) ? sanitize_text_field($data['utm_medium']) : '',
            'utm_campaign' => !empty($data['utm_campaign']) ? sanitize_text_field($data['utm_campaign']) : '',
            'utm_term' => !empty($data['utm_term']) ? sanitize_text_field($data['utm_term']) : '',
            'utm_content' => !empty($data['utm_content']) ? sanitize_text_field($data['utm_content']) : '',
            'gclid' => !empty($data['gclid']) ? sanitize_text_field($data['gclid']) : '',
            'fbclid' => !empty($data['fbclid']) ? sanitize_text_field($data['fbclid']) : '',
            'extra' => $extra,
        ),
        array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%f','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')
    );

    return $inserted !== false;
}

function osl_cq_activity_where_sql($event_filter = '') {
    $event_filter = osl_cq_activity_sanitize_event($event_filter);
    $where = 'WHERE 1=1';
    $params = array();

    if ($event_filter !== '') {
        $where .= ' AND event_name = %s';
        $params[] = $event_filter;
    }

    return array($where, $params);
}

function osl_cq_get_recent_activity_events($limit = 100, $offset = 0, $event_filter = '') {
    global $wpdb;

    if (!osl_cq_activity_table_exists()) return array();

    $limit = min(100, max(1, absint($limit)));
    $offset = max(0, absint($offset));
    list($where, $params) = osl_cq_activity_where_sql($event_filter);

    $sql = 'SELECT * FROM ' . osl_cq_activity_table_name() . ' ' . $where . ' ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d';
    $params[] = $limit;
    $params[] = $offset;

    return $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
}

function osl_cq_count_activity_events($event_filter = '') {
    global $wpdb;

    if (!osl_cq_activity_table_exists()) return 0;

    list($where, $params) = osl_cq_activity_where_sql($event_filter);
    $sql = 'SELECT COUNT(*) FROM ' . osl_cq_activity_table_name() . ' ' . $where;

    if (!empty($params)) {
        $sql = $wpdb->prepare($sql, $params);
    }

    return absint($wpdb->get_var($sql));
}

function osl_cq_activity_extra_value($event, $key) {
    if (empty($event['extra'])) return '';

    $extra = json_decode($event['extra'], true);
    if (!is_array($extra) || !isset($extra[$key])) return '';

    return sanitize_text_field($extra[$key]);
}

function osl_cq_activity_admin_url($args = array()) {
    return add_query_arg(array_merge(array('page' => 'osl-cq-activity'), $args), admin_url('admin.php'));
}

function osl_cq_activity_redirect($args = array()) {
    wp_safe_redirect(osl_cq_activity_admin_url($args));
    exit;
}

function osl_cq_export_activity_csv($event_filter = '') {
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions.');
    }

    check_admin_referer('osl_cq_export_activity');

    $event_filter = osl_cq_activity_sanitize_event($event_filter);
    $events = osl_cq_get_recent_activity_events(100, 0, $event_filter);

    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=djr-quote-activity-' . gmdate('Ymd-His') . '.csv');

    $out = fopen('php://output', 'w');

    fputcsv($out, array(
        'Date/time',
        'Event',
        'Source',
        'Quote token',
        'Transaction type',
        'Property type',
        'Council',
        'Suburb',
        'Page path',
        'Quote total',
        'Quote band',
        'CTA/link URL',
        'Email',
        'Phone',
        'UTM source',
        'UTM medium',
        'UTM campaign',
    ));

    foreach ($events as $event) {
        fputcsv($out, array(
            $event['created_at'] ?? '',
            $event['event_name'] ?? '',
            $event['event_source'] ?? '',
            $event['quote_token'] ?? '',
            $event['transaction_type'] ?? '',
            $event['property_type'] ?? '',
            $event['council'] ?? '',
            $event['suburb'] ?? '',
            $event['page_path'] ?? '',
            $event['quote_total'] ?? '',
            $event['quote_total_band'] ?? '',
            osl_cq_activity_extra_value($event, 'link_url'),
            $event['email'] ?? '',
            $event['phone'] ?? '',
            $event['utm_source'] ?? '',
            $event['utm_medium'] ?? '',
            $event['utm_campaign'] ?? '',
        ));
    }

    fclose($out);
    exit;
}

function osl_cq_delete_selected_activity($ids) {
    global $wpdb;

    $ids = array_filter(array_map('absint', (array) $ids));
    if (empty($ids) || !osl_cq_activity_table_exists()) return 0;

    $placeholders = implode(',', array_fill(0, count($ids), '%d'));
    $sql = 'DELETE FROM ' . osl_cq_activity_table_name() . ' WHERE id IN (' . $placeholders . ')';

    return absint($wpdb->query($wpdb->prepare($sql, $ids)));
}

function osl_cq_delete_activity_older_than($days) {
    global $wpdb;

    $days = absint($days);
    if (!in_array($days, array(30, 90, 180, 365), true) || !osl_cq_activity_table_exists()) return 0;

    $cutoff = gmdate('Y-m-d H:i:s', time() - ($days * DAY_IN_SECONDS));

    return absint($wpdb->query(
        $wpdb->prepare(
            'DELETE FROM ' . osl_cq_activity_table_name() . ' WHERE created_at < %s',
            $cutoff
        )
    ));
}

function osl_cq_delete_filtered_activity($event_filter = '') {
    global $wpdb;

    if (!osl_cq_activity_table_exists()) return 0;

    list($where, $params) = osl_cq_activity_where_sql($event_filter);
    $sql = 'DELETE FROM ' . osl_cq_activity_table_name() . ' ' . $where;

    if (!empty($params)) {
        $sql = $wpdb->prepare($sql, $params);
    }

    return absint($wpdb->query($sql));
}

function osl_cq_maybe_run_activity_retention_cleanup() {
    if (!is_admin() || !current_user_can('manage_options')) return;

    $retention = get_option('osl_cq_quote_activity_retention_days', 'forever');
    if (!array_key_exists($retention, osl_cq_activity_retention_options()) || $retention === 'forever') return;
    if (get_transient('osl_cq_quote_activity_retention_ran')) return;

    osl_cq_delete_activity_older_than(absint($retention));
    set_transient('osl_cq_quote_activity_retention_ran', 1, DAY_IN_SECONDS);
}

function osl_cq_handle_activity_admin_actions() {
    if (!is_admin() || !current_user_can('manage_options')) return;
    if (($_GET['page'] ?? '') !== 'osl-cq-activity' && ($_POST['page'] ?? '') !== 'osl-cq-activity') return;

    osl_cq_maybe_create_activity_table();

    $action = sanitize_key($_REQUEST['osl_cq_activity_action'] ?? '');

    if ($action === 'export') {
        osl_cq_export_activity_csv($_GET['event_name'] ?? '');
    }

    if ($action === 'save_retention') {
        check_admin_referer('osl_cq_save_activity_retention');
        $retention = sanitize_text_field($_POST['retention_days'] ?? 'forever');
        if (!array_key_exists($retention, osl_cq_activity_retention_options())) {
            $retention = 'forever';
        }
        update_option('osl_cq_quote_activity_retention_days', $retention, false);
        osl_cq_activity_redirect(array('osl_cq_notice' => 'retention_saved'));
    }

    if ($action === 'delete_selected') {
        check_admin_referer('osl_cq_activity_bulk_delete');
        $deleted = osl_cq_delete_selected_activity($_POST['activity_ids'] ?? array());
        osl_cq_activity_redirect(array('osl_cq_deleted' => $deleted));
    }

    if ($action === 'delete_older_than') {
        check_admin_referer('osl_cq_activity_delete_older_than');
        $deleted = osl_cq_delete_activity_older_than(absint($_POST['delete_older_than_days'] ?? 0));
        osl_cq_activity_redirect(array('osl_cq_deleted' => $deleted));
    }

    if ($action === 'confirm_delete') {
        check_admin_referer('osl_cq_activity_confirm_delete');
        $scope = sanitize_key($_POST['delete_scope'] ?? '');
        $event_filter = osl_cq_activity_sanitize_event($_POST['event_name'] ?? '');

        if ($scope === 'filtered' && $event_filter !== '') {
            $deleted = osl_cq_delete_filtered_activity($event_filter);
            osl_cq_activity_redirect(array('event_name' => $event_filter, 'osl_cq_deleted' => $deleted));
        }

        if ($scope === 'all') {
            $deleted = osl_cq_delete_filtered_activity('');
            osl_cq_activity_redirect(array('osl_cq_deleted' => $deleted));
        }
    }
}
add_action('admin_init', 'osl_cq_handle_activity_admin_actions');
add_action('admin_init', 'osl_cq_maybe_run_activity_retention_cleanup');

function osl_cq_ajax_log_activity() {
    check_ajax_referer('osl_cq_nonce', 'nonce');

    $event_name = osl_cq_activity_sanitize_event($_POST['event_name'] ?? '');
    if ($event_name === '' || $event_name === 'quote_generated') {
        wp_send_json_error(array('message' => 'Invalid event.'));
    }

    $payload = osl_cq_collect_activity_payload($_POST);
    $data = array_merge($payload, array(
        'transaction_type' => sanitize_text_field($_POST['transaction_type'] ?? ''),
        'property_type' => sanitize_text_field($_POST['property_type'] ?? ''),
        'council' => sanitize_text_field($_POST['council'] ?? ''),
        'quote_total' => isset($_POST['quote_total']) ? floatval($_POST['quote_total']) : null,
        'quote_total_band' => sanitize_text_field($_POST['quote_total_band'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'extra' => array(
            'cta_location' => sanitize_text_field($_POST['cta_location'] ?? ''),
            'link_url' => esc_url_raw($_POST['link_url'] ?? ''),
        ),
    ));

    osl_cq_log_activity($event_name, $data, 'browser');
    wp_send_json_success(array('message' => 'logged'));
}
add_action('wp_ajax_osl_cq_log_event', 'osl_cq_ajax_log_activity');
add_action('wp_ajax_nopriv_osl_cq_log_event', 'osl_cq_ajax_log_activity');

function osl_cq_register_activity_admin_page() {
    add_submenu_page(
        'osl-cq-pricing',
        'Quote Activity',
        'Quote Activity',
        'manage_options',
        'osl-cq-activity',
        'osl_cq_render_activity_page'
    );
}
add_action('admin_menu', 'osl_cq_register_activity_admin_page', 30);

function osl_cq_render_activity_page() {
    if (!current_user_can('manage_options')) return;

    osl_cq_maybe_create_activity_table();

    $per_page = 100;
    $paged = max(1, absint($_GET['paged'] ?? 1));
    $offset = ($paged - 1) * $per_page;
    $event_filter = osl_cq_activity_sanitize_event($_GET['event_name'] ?? '');
    $events = osl_cq_get_recent_activity_events($per_page, $offset, $event_filter);
    $total = osl_cq_count_activity_events($event_filter);
    $total_pages = max(1, (int) ceil($total / $per_page));

    $export_args = array('osl_cq_activity_action' => 'export');
    if ($event_filter !== '') $export_args['event_name'] = $event_filter;
    $export_url = wp_nonce_url(osl_cq_activity_admin_url($export_args), 'osl_cq_export_activity');
    $confirm_delete = sanitize_key($_GET['confirm_delete'] ?? '');
    ?>
    <div class="wrap osl-cq-wrap osl-cq-activity-wrap">
        <h1>Quote Activity <a class="page-title-action" href="<?php echo esc_url($export_url); ?>">Export CSV</a></h1>

        <?php if (isset($_GET['osl_cq_deleted'])): ?>
            <div class="notice notice-success is-dismissible"><p><?php echo esc_html(absint($_GET['osl_cq_deleted'])); ?> quote activity record(s) deleted.</p></div>
        <?php endif; ?>
        <?php if (($_GET['osl_cq_notice'] ?? '') === 'retention_saved'): ?>
            <div class="notice notice-success is-dismissible"><p>Quote activity retention setting saved.</p></div>
        <?php endif; ?>

        <p>Recent conveyancing quote generation and quote-result CTA events. Showing up to <?php echo esc_html($per_page); ?> records per page.</p>
        <p class="description">quote_started records first interaction. quote_generated records a completed quote. CTA clicked and Email columns populate only when a visitor clicks a result action or voluntarily supplies email details.</p>

        <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" style="margin:16px 0;">
            <input type="hidden" name="page" value="osl-cq-activity">
            <label for="event_name">Filter by event</label>
            <select name="event_name" id="event_name">
                <option value="">All events</option>
                <?php foreach (osl_cq_activity_allowed_events() as $event_name): ?>
                    <option value="<?php echo esc_attr($event_name); ?>" <?php selected($event_filter, $event_name); ?>><?php echo esc_html($event_name); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="button">Filter</button>
            <a class="button button-primary" href="<?php echo esc_url($export_url); ?>">Export CSV</a>
        </form>

        <div class="postbox" style="padding:14px 16px;margin-top:16px;">
            <h2 style="margin-top:0;">Retention and deletion tools</h2>

            <form method="post" style="margin-bottom:12px;">
                <input type="hidden" name="page" value="osl-cq-activity">
                <input type="hidden" name="osl_cq_activity_action" value="save_retention">
                <?php wp_nonce_field('osl_cq_save_activity_retention'); ?>
                <label for="retention_days">Automatic retention cleanup</label>
                <select name="retention_days" id="retention_days">
                    <?php foreach (osl_cq_activity_retention_options() as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected(get_option('osl_cq_quote_activity_retention_days', 'forever'), $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="button">Save retention</button>
                <p class="description">Cleanup runs from WordPress admin requests at most once daily, never on the public quote form.</p>
            </form>

            <form method="post" style="margin-bottom:12px;">
                <input type="hidden" name="page" value="osl-cq-activity">
                <input type="hidden" name="osl_cq_activity_action" value="delete_older_than">
                <?php wp_nonce_field('osl_cq_activity_delete_older_than'); ?>
                <label for="delete_older_than_days">Delete records older than</label>
                <select name="delete_older_than_days" id="delete_older_than_days">
                    <option value="30">30 days</option>
                    <option value="90">90 days</option>
                    <option value="180">180 days</option>
                    <option value="365">365 days</option>
                </select>
                <button class="button" onclick="return confirm('Delete old quote activity records?');">Delete old records</button>
            </form>

            <?php if ($event_filter !== ''): ?>
                <p><a class="button" href="<?php echo esc_url(osl_cq_activity_admin_url(array('event_name' => $event_filter, 'confirm_delete' => 'filtered'))); ?>">Delete all filtered activity</a></p>
            <?php endif; ?>
            <p><a class="button" href="<?php echo esc_url(osl_cq_activity_admin_url(array('confirm_delete' => 'all'))); ?>">Delete all activity</a></p>

            <?php if ($confirm_delete === 'all' || ($confirm_delete === 'filtered' && $event_filter !== '')): ?>
                <div class="notice notice-warning inline">
                    <p><strong>Confirm deletion.</strong> This permanently deletes quote activity records only.</p>
                    <form method="post">
                        <input type="hidden" name="page" value="osl-cq-activity">
                        <input type="hidden" name="osl_cq_activity_action" value="confirm_delete">
                        <input type="hidden" name="delete_scope" value="<?php echo esc_attr($confirm_delete); ?>">
                        <input type="hidden" name="event_name" value="<?php echo esc_attr($event_filter); ?>">
                        <?php wp_nonce_field('osl_cq_activity_confirm_delete'); ?>
                        <button class="button button-primary">Yes, delete <?php echo $confirm_delete === 'filtered' ? 'filtered' : 'all'; ?> activity</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <h2>Recent activity <a class="button button-primary" href="<?php echo esc_url($export_url); ?>">Export CSV</a></h2>
        <p><?php echo esc_html($total); ?> event(s) logged<?php echo $event_filter ? ' for ' . esc_html($event_filter) : ''; ?>.</p>

        <form method="post">
            <input type="hidden" name="page" value="osl-cq-activity">
            <input type="hidden" name="osl_cq_activity_action" value="delete_selected">
            <?php wp_nonce_field('osl_cq_activity_bulk_delete'); ?>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th style="width:32px;"><input type="checkbox" onclick="jQuery('.osl-cq-activity-checkbox').prop('checked', this.checked);"></th>
                        <th>Date/time</th>
                        <th>Event</th>
                        <th>Source</th>
                        <th>Quote token</th>
                        <th>Transaction type</th>
                        <th>Property type</th>
                        <th>Council</th>
                        <th>Suburb / page path</th>
                        <th>Quote total / band</th>
                        <th>CTA clicked</th>
                        <th>Email</th>
                        <th>Page</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr><td colspan="13">No quote activity has been logged yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><input class="osl-cq-activity-checkbox" type="checkbox" name="activity_ids[]" value="<?php echo esc_attr(absint($event['id'])); ?>"></td>
                                <td><?php echo esc_html($event['created_at']); ?></td>
                                <td><code><?php echo esc_html($event['event_name']); ?></code></td>
                                <td><?php echo esc_html($event['event_source']); ?></td>
            
                                <td><?php echo esc_html($event['transaction_type']); ?></td>
                                <td><?php echo esc_html($event['property_type']); ?></td>
                                <td><?php echo esc_html($event['council']); ?></td>
                                <td>
                                    <?php if (!empty($event['suburb'])): ?>
                                        <strong><?php echo esc_html($event['suburb']); ?></strong><br>
                                    <?php endif; ?>
                                    <span class="osl-cq-muted"><?php echo esc_html($event['page_path']); ?></span>
                                </td>
                                <td>
                                    <?php
                                    if ($event['quote_total'] !== null && $event['quote_total'] !== '') {
                                        echo esc_html('$' . number_format((float) $event['quote_total'], 2));
                                    }
                                    if (!empty($event['quote_total_band'])) {
                                        echo '<br><span class="osl-cq-muted">' . esc_html($event['quote_total_band']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo esc_html(osl_cq_activity_extra_value($event, 'link_url')); ?></td>
                                <td><?php echo esc_html($event['email']); ?></td>
                                <td>
                                    <?php if (!empty($event['page_url'])): ?>
                                        <a href="<?php echo esc_url($event['page_url']); ?>" target="_blank" rel="noopener">Open source page</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <p><button class="button" onclick="return confirm('Delete selected quote activity records?');">Delete selected</button></p>
        </form>

        <?php if ($total_pages > 1): ?>
            <div class="tablenav bottom"><div class="tablenav-pages">
                <?php echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_pages,
                    'current' => $paged,
                )); ?>
            </div></div>
        <?php endif; ?>
    </div>
    <?php
}
