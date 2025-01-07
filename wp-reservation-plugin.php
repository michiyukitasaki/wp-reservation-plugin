<?php
/*
Plugin Name: WP Reservation Plugin
Description: A WordPress plugin to manage reservations with customizable settings and a user-friendly interface.
Version: 1.0.0
Author: Your Name
License: GPL-2.0-or-later
*/

if (!defined('ABSPATH')) exit; // セキュリティのため直接アクセスを防止

// 必要なファイルをインクルード
require_once plugin_dir_path(__FILE__) . 'includes/db-setup.php';
require_once plugin_dir_path(__FILE__) . 'includes/reservation-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-handler.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-settings.php';

// プラグインのアクティベーション時にデータベーステーブルを作成
register_activation_hook(__FILE__, 'wp_reservation_db_setup');

// 管理メニューの追加
add_action('admin_menu', 'wp_reservation_admin_menu');
function wp_reservation_admin_menu() {
    add_menu_page(
        'Reservation Settings',        // ページタイトル
        'Reservations',                // メニュータイトル
        'manage_options',              // 必要な権限
        'wp-reservation-settings',     // スラッグ
        'wp_reservation_settings_page',// コールバック関数
        'dashicons-calendar-alt'       // アイコン
    );
}

// スクリプトとスタイルの読み込み
add_action('wp_enqueue_scripts', 'wp_reservation_enqueue_scripts');
function wp_reservation_enqueue_scripts() {
    wp_enqueue_style('wp-reservation-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('wp-reservation-script', plugin_dir_url(__FILE__) . 'assets/js/reservation-app.js', [], false, true);

    // 予約可能な曜日と時間帯の設定をJavaScriptに渡す
    wp_localize_script('wp-reservation-script', 'wpReservationSettings', [
        'reservationDays' => get_option('wp_reservation_days', []),
        'timeSlots' => explode(',', get_option('wp_reservation_time_slots', ''))
    ]);
}

// ショートコードの登録
add_shortcode('wp_reservation_form', 'wp_reservation_form_shortcode');
function wp_reservation_form_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/reservation-form.php';
    return ob_get_clean();
}

// 管理画面の予約情報表示（管理ページ用サブメニュー）
add_action('admin_menu', 'wp_reservation_admin_submenu');
function wp_reservation_admin_submenu() {
    add_submenu_page(
        'wp-reservation-settings',     // 親メニューのスラッグ
        'Manage Reservations',         // サブメニューのページタイトル
        'Manage Reservations',         // サブメニューの名前
        'manage_options',              // 必要な権限
        'wp-reservation-manage',       // サブメニューのスラッグ
        'wp_reservation_manage_page'   // コールバック関数
    );
}

// 予約管理ページの表示関数
function wp_reservation_manage_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    $reservations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date, time_slot");

    ?>
    <div class="wrap">
        <h1>Manage Reservations</h1>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservations)): ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo esc_html($reservation->id); ?></td>
                            <td><?php echo esc_html($reservation->date); ?></td>
                            <td><?php echo esc_html($reservation->time_slot); ?></td>
                            <td><?php echo esc_html($reservation->name); ?></td>
                            <td><?php echo esc_html($reservation->phone); ?></td>
                            <td><?php echo esc_html($reservation->email); ?></td>
                            <td><?php echo esc_html($reservation->notes); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No reservations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
