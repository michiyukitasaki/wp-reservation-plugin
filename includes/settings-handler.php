<?php
if (!defined('ABSPATH')) exit;

// Register settings
add_action('admin_init', 'wp_reservation_register_settings');
function wp_reservation_register_settings() {
    register_setting('wp_reservation_settings', 'wp_reservation_days');
    register_setting('wp_reservation_settings', 'wp_reservation_time_slots');
    register_setting('wp_reservation_settings', 'wp_reservation_max_people');
}

// Save settings via REST API
add_action('rest_api_init', function () {
    register_rest_route('wp-reservation/v1', '/settings', [
        'methods' => 'POST',
        'callback' => 'wp_reservation_save_settings',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ]);
});

function wp_reservation_save_settings($request) {
    $days = $request->get_param('days');
    $time_slots = $request->get_param('time_slots');
    $max_people = $request->get_param('max_people');

    if (!is_array($days) || empty($time_slots) || !is_numeric($max_people)) {
        return new WP_Error('invalid_input', 'Invalid input provided.', ['status' => 400]);
    }

    update_option('wp_reservation_days', array_map('sanitize_text_field', $days));
    update_option('wp_reservation_time_slots', sanitize_text_field($time_slots));
    update_option('wp_reservation_max_people', intval($max_people));

    return ['success' => true];
}
?>
