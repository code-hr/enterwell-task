<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_theme_support('post-thumbnails');
add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
add_theme_support('custom-logo');

add_action('init', function() {
    register_nav_menus([
        'header-menu' => __('Header Menu'),
        'footer-menu' => __('Footer Menu')
    ]);
});

function theme_enqueue_assets() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
    wp_enqueue_style('main-scss', get_template_directory_uri() . '/scss/main.css', [], '1.0.0');

    wp_enqueue_script('enterwell-js', get_template_directory_uri() . '/js/enterwell.js', ['jquery'], '1.0.0', true);

    wp_localize_script('enterwell-js', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('check_user_exists_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

function enqueue_custom_admin_styles() {
    wp_enqueue_style('admin-main-css', get_template_directory_uri() . '/scss/dashboard/dashboard-menu.css', array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_styles');




// AJAX check user exists
add_action('wp_ajax_check_user_exists', 'check_user_exists');
add_action('wp_ajax_nopriv_check_user_exists', 'check_user_exists');

function check_user_exists() {
    check_ajax_referer('check_user_exists_nonce', '_ajax_nonce');

    if (!isset($_POST['email']) || !isset($_POST['account_number'])) {
        wp_send_json_error(['message' => 'Nedostaju podaci']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'enterwell_giveaway_form';
    $email = sanitize_email($_POST['email']);
    $account_number = sanitize_text_field($_POST['account_number']);

    $email_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email));
    $account_number_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE account_number = %s", $account_number));

    wp_send_json([
        'email_exists' => $email_exists > 0,
        'account_number_exists' => $account_number_exists > 0
    ]);
}

// Form submission
add_action('init', 'handle_form_submission');
function handle_form_submission() {
    if (!isset($_POST['submit_form'])) return;

    // Nonce verification
    if (!isset($_POST['enterwell_nonce']) || !wp_verify_nonce($_POST['enterwell_nonce'], 'enterwell_giveaway_form')) {
        wp_die('Sigurnosna provjera nije prošla.');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'enterwell_giveaway_form';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            document varchar(255) NOT NULL,
            account_number varchar(255) NOT NULL,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            address varchar(255) NOT NULL,
            house_number varchar(255) NOT NULL,
            city varchar(255) NOT NULL,
            zip_code varchar(255) NOT NULL,
            country varchar(255) NOT NULL,
            mobile varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    $document_url = '';
    if (!empty($_FILES['document']['name'])) {
        $allowed_types = ['application/pdf', 'image/png', 'image/jpeg'];
        if (in_array($_FILES['document']['type'], $allowed_types)) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $upload = wp_handle_upload($_FILES['document'], ['test_form' => false]);
            if (!isset($upload['error'])) {
                $document_url = esc_url_raw($upload['url']);
            }
        }
    }

    $data = [
        'document'       => $document_url,
        'account_number' => sanitize_text_field($_POST['account_number']),
        'first_name'     => sanitize_text_field($_POST['first_name']),
        'last_name'      => sanitize_text_field($_POST['last_name']),
        'address'        => sanitize_text_field($_POST['address']),
        'house_number'   => sanitize_text_field($_POST['house_number']),
        'city'           => sanitize_text_field($_POST['city']),
        'zip_code'       => sanitize_text_field($_POST['zip_code']),
        'country'        => sanitize_text_field($_POST['country']),
        'mobile'         => sanitize_text_field($_POST['mobile']),
        'email'          => sanitize_email($_POST['email']),
    ];

    $result = $wpdb->insert($table, $data);

    if ($result) {
        $name    = esc_html($data['first_name']);
        $to      = sanitize_email($data['email']);
        $subject = 'Potvrda prijave na nagradnu igru';
        $message = "Poštovani $name,\n\nHvala vam na prijavi ...";
        $site_name = get_bloginfo('name');
        $domain = wp_parse_url(site_url(), PHP_URL_HOST);
        $from_email = 'noreply@' . $domain;
        
        $headers = [
            "From: $site_name <$from_email>",
            "Reply-To: $to",
            'Content-Type: text/plain; charset=UTF-8'
        ];
        
        $sent = wp_mail($to, $subject, $message, $headers);        
    
        if ($sent) {
            wp_safe_redirect(home_url('/rezultati-prijave?success=true'));
        } else {
            wp_safe_redirect(home_url('/rezultati-prijave?failed=true'));
        }
    } else {
        wp_safe_redirect(home_url('/rezultati-prijave?failed=true'));
    }
    
    exit;
}

// Add a new menu page in the WordPress admin dashboard
add_action('admin_menu', 'register_custom_menu_page');

function register_custom_menu_page() {
    add_menu_page(
        __('Giveaway Form Entries', 'textdomain'), // Page title
        __('Giveaway Form Entries', 'textdomain'), // Menu title
        'manage_options',                 // Capability
        'giveaway-form-entries',                   // Menu slug
        'display_form_entries',           // Callback function
        'dashicons-list-view',            // Icon
        6                                 // Position
    );
}


// dashboard menu
function display_form_entries() {
    global $wpdb;
    $table = $wpdb->prefix . 'enterwell_giveaway_form';

    $entry_count = $wpdb->get_var("SELECT COUNT(*) FROM $table");

    $entries = $wpdb->get_results("SELECT * FROM $table");

    echo '<div class="table-entries-wrapper">';
    echo '<h1>' . __('Giveaway Form Entries', 'textdomain') . '</h1>';
    echo '<p>' . sprintf(__('Total Entries: %d', 'textdomain'), $entry_count) . '</p>';
    
    if ($entries) {
        echo '<table class="widefat table-entries fixed" cellspacing="0">';
        echo '<thead><tr>';
        echo '<th>' . __('ID', 'textdomain') . '</th>';
        echo '<th>' . __('Document', 'textdomain') . '</th>';
        echo '<th>' . __('Account Number', 'textdomain') . '</th>';
        echo '<th>' . __('First Name', 'textdomain') . '</th>';
        echo '<th>' . __('Last Name', 'textdomain') . '</th>';
        echo '<th>' . __('Email', 'textdomain') . '</th>';
        echo '<th>' . __('Address', 'textdomain') . '</th>';
        echo '<th>' . __('House Number', 'textdomain') . '</th>';
        echo '<th>' . __('City', 'textdomain') . '</th>';
        echo '<th>' . __('Zip Code', 'textdomain') . '</th>';
        echo '<th>' . __('Country', 'textdomain') . '</th>';
        echo '<th>' . __('Mobile', 'textdomain') . '</th>';
        echo '</tr></thead><tbody>';

        foreach ($entries as $entry) {
            echo '<tr>';
            echo '<td>' . esc_html($entry->id) . '</td>';
            echo '<td>';
            if (!empty($entry->document)) {
                echo '<a href="' . esc_url($entry->document) . '" target="_blank">' . 'Link' . '</a>';
            }
            echo '</td>';
            echo '<td>' . esc_html($entry->account_number) . '</td>';
            echo '<td>' . esc_html($entry->first_name) . '</td>';
            echo '<td>' . esc_html($entry->last_name) . '</td>';
            echo '<td>' . esc_html($entry->email) . '</td>';
            echo '<td>' . esc_html($entry->address) . '</td>';
            echo '<td>' . esc_html($entry->house_number) . '</td>';
            echo '<td>' . esc_html($entry->city) . '</td>';
            echo '<td>' . esc_html($entry->zip_code) . '</td>';
            echo '<td>' . esc_html($entry->country) . '</td>';
            echo '<td>' . esc_html($entry->mobile) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>' . __('No entries found.', 'textdomain') . '</p>';
    }

    echo '</div>';
}