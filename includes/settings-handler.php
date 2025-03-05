<?php
if (!defined('ABSPATH')) exit;

// Register settings
add_action('admin_init', 'easyresy_register_settings');
function easyresy_register_settings() {
    register_setting('easyresy_reservation_settings', 'easyresy_reservation_days', [
        'sanitize_callback' => 'easyresy_reservation_sanitize_days'
    ]);
    register_setting('easyresy_reservation_settings', 'easyresy_reservation_time_slots', [
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    register_setting('easyresy_reservation_settings', 'easyresy_reservation_max_people', [
        'sanitize_callback' => 'intval'
    ]);
}

// Save settings via REST API
add_action('rest_api_init', function () {
    register_rest_route('wp-reservation/v1', '/settings', [
        'methods' => 'POST',
        'callback' => 'easyresy_reservation_save_settings',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ]);
});

function easyresy_reservation_save_settings($request) {
    $days = $request->get_param('days');
    $time_slots = $request->get_param('time_slots');
    $max_people = $request->get_param('max_people');

    if (!is_array($days) || empty($time_slots) || !is_numeric($max_people)) {
        return new WP_Error('invalid_input', 'Invalid input provided.', ['status' => 400]);
    }

    update_option('easyresy_reservation_days', array_map('sanitize_text_field', $days));
    update_option('easyresy_reservation_time_slots', sanitize_text_field($time_slots));
    update_option('easyresy_reservation_max_people', intval($max_people));

    return ['success' => true];
}

// Sanitize callback for reservation days
function easyresy_reservation_sanitize_days($input) {
    $valid_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $output = [];

    foreach ($input as $day) {
        if (in_array($day, $valid_days)) {
            $output[] = $day;
        }
    }

    return $output;
}