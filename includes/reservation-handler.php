<?php
if (!defined('ABSPATH')) exit;

// Handle reservation API endpoint
add_action('rest_api_init', function () {
    register_rest_route('wp-reservation/v1', '/reserve', [
        'methods' => 'POST',
        'callback' => 'wp_reservation_save_reservation',
        'permission_callback' => '__return_true'
    ]);

    // 予約済みの数を取得するエンドポイント
    register_rest_route('wp-reservation/v1', '/availability', [
        'methods' => 'GET',
        'callback' => 'wp_reservation_get_availability',
        'permission_callback' => '__return_true'
    ]);
});

function wp_reservation_save_reservation($request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    $date = sanitize_text_field($request->get_param('reservation_date'));
    $time_slot = sanitize_text_field($request->get_param('time_slot'));
    $name = sanitize_text_field($request->get_param('name'));
    $phone = sanitize_text_field($request->get_param('phone'));
    $email = sanitize_email($request->get_param('email'));
    $notes = sanitize_textarea_field($request->get_param('notes'));

    if (empty($date) || empty($time_slot) || empty($name) || empty($phone) || empty($email)) {
        return new WP_Error('missing_fields', 'All fields are required.', ['status' => 400]);
    }

    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE date = %s AND time_slot = %s",
        $date, $time_slot
    ));

    $max_people = get_option('wp_reservation_max_people', 3);
    if ($count >= $max_people) {
        return new WP_Error('slot_full', 'This time slot is fully booked.', ['status' => 400]);
    }

    $wpdb->insert($table_name, [
        'date' => $date,
        'time_slot' => $time_slot,
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'notes' => $notes,
    ]);

    return ['success' => true];
}

function wp_reservation_get_availability($request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    $date = sanitize_text_field($request->get_param('date'));
    $time_slot = sanitize_text_field($request->get_param('time_slot'));

    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE date = %s AND time_slot = %s",
        $date, $time_slot
    ));

    $max_people = get_option('wp_reservation_max_people', 3);
    $available = $max_people - $count;

    return ['available' => $available];
}
?>
