<?php
/*
Plugin Name: Easy Reservation Systems
Description: A WordPress plugin to manage reservations with customizable settings and a user-friendly interface.
Version: 1.0.0
Author: Michiyuki Tasaki
License: GPL-2.0-or-later
*/

if (!defined('ABSPATH')) exit; // セキュリティのため直接アクセスを防止

// 必要なファイルをインクルード
require_once plugin_dir_path(__FILE__) . 'includes/db-setup.php';
require_once plugin_dir_path(__FILE__) . 'includes/reservation-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-handler.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-settings.php';

// プラグインのアクティベーション時にデータベーステーブルを作成
register_activation_hook(__FILE__, 'easyresy_db_setup');

// 管理メニューの追加
add_action('admin_menu', 'easyresy_admin_menu');
function easyresy_admin_menu() {
    add_menu_page(
        '予約システム設定',            // ページタイトル
        '予約システムの設定',                // メニュータイトル
        'manage_options',              // 必要な権限
        'easyresy-settings',     // スラッグ
        'easyresy_settings_page',// コールバック関数
        'dashicons-calendar-alt'       // アイコン
    );
    add_submenu_page(
        'easyresy-settings',     // 親メニューのスラッグ
        '予約情報一覧',                    // サブメニューのページタイトル
        '予約情報一覧',                    // サブメニューの名前
        'manage_options',              // 必要な権限
        'easyresy-manage',       // サブメニューのスラッグ
        'easyresy_manage_page'   // コールバック関数
    );
}

// スクリプトとスタイルの読み込み
add_action('wp_enqueue_scripts', 'easyresy_enqueue_scripts');
function easyresy_enqueue_scripts() {
    wp_enqueue_style('easyresy-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('easyresy-script', plugin_dir_url(__FILE__) . 'assets/js/reservation-app.js', [], false, true);

    // 予約可能な曜日と時間帯の設定をJavaScriptに渡す
    wp_localize_script('easyresy-script', 'easyresySettings', [
        'reservationDays' => get_option('easyresy_reservation_days', []),
        'timeSlots' => explode(',', get_option('easyresy_reservation_time_slots', ''))
    ]);
}

function easyresy_load_font_awesome() {
    wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . 'assets/css/font-awesome.min.css');
}
add_action('wp_enqueue_scripts', 'easyresy_load_font_awesome');
add_action('admin_enqueue_scripts', 'easyresy_load_font_awesome');

// テーマカラーを動的に適用
function easyresy_dynamic_styles() {
    $theme_color = esc_attr(get_option('easyresy_reservation_theme_color', '#8B4513'));
    $theme_color_dark = esc_attr(adjustBrightness($theme_color, -40));
    $theme_color_light = esc_attr(adjustBrightness($theme_color, 40));
    wp_add_inline_style('easyresy-style', "
        :root {
            --theme-color: {$theme_color};
            --theme-color-dark: {$theme_color_dark};
            --theme-color-light: {$theme_color_light};
        }
    ");
}
add_action('wp_enqueue_scripts', 'easyresy_dynamic_styles');

// 明るさを調整する関数
function adjustBrightness($hex, $steps) {
    // Ensure steps is between -255 and 255
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color = hexdec($color); // Convert to decimal
        $color = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

// ショートコードの登録
add_shortcode('easyresy_reservation_system', 'easyresy_form_shortcode');
function easyresy_form_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/reservation-form.php';
    return ob_get_clean();
}

// 予約管理ページの表示関数
function easyresy_manage_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    // フィルタリング
    $filter_date = isset($_GET['filter_date']) ? sanitize_text_field($_GET['filter_date']) : '';
    $filter_month = isset($_GET['filter_month']) ? sanitize_text_field($_GET['filter_month']) : '';
    $filter_name = isset($_GET['filter_name']) ? sanitize_text_field($_GET['filter_name']) : '';

    $query = "SELECT * FROM $table_name WHERE 1=1";
    if ($filter_date) {
        $query .= $wpdb->prepare(" AND date = %s", $filter_date);
    }
    if ($filter_month) {
        $query .= $wpdb->prepare(" AND DATE_FORMAT(date, '%Y-%m') = %s", $filter_month);
    }
    if ($filter_name) {
        $query .= $wpdb->prepare(" AND name LIKE %s", '%' . $wpdb->esc_like($filter_name) . '%');
    }
    $query .= " ORDER BY date, time_slot";

    $reservations = $wpdb->get_results($query);

    // 予約の削除
    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $wpdb->delete($table_name, ['id' => $delete_id]);
        echo '<div class="updated"><p>予約が削除されました。</p></div>';
    }

    // 予約の編集
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
        $edit_id = intval($_POST['edit_id']);
        $date = sanitize_text_field($_POST['date']);
        $time_slot = sanitize_text_field($_POST['time_slot']);
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);
        $notes = sanitize_textarea_field($_POST['notes']);

        $wpdb->update($table_name, [
            'date' => $date,
            'time_slot' => $time_slot,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'notes' => $notes,
        ], ['id' => $edit_id]);

        echo '<div class="updated"><p>予約が更新されました。</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>予約情報一覧</h1>
        <div class="filter-box">
            <form method="GET" action="">
                <input type="hidden" name="page" value="easyresy-manage">
                <div class="filter-item">
                    <label for="filter_date"><strong>日付でフィルタリング:</strong></label>
                    <input type="date" id="filter_date" name="filter_date" value="<?php echo esc_attr($filter_date); ?>">
                </div>
                <div class="filter-item">
                    <label for="filter_month"><strong>月でフィルタリング:</strong></label>
                    <input type="month" id="filter_month" name="filter_month" value="<?php echo esc_attr($filter_month); ?>">
                </div>
                <div class="filter-item">
                    <label for="filter_name"><strong>名前でフィルタリング:</strong></label>
                    <input type="text" id="filter_name" name="filter_name" placeholder="名前で検索" value="<?php echo esc_attr($filter_name); ?>">
                </div>
                <button type="submit" class="button button-primary">検索</button>
            </form>
        </div>
        <div class="data-box">
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>日付</th>
                        <th>時間帯</th>
                        <th>名前</th>
                        <th>電話番号</th>
                        <th>メールアドレス</th>
                        <th>備考</th>
                        <th>操作</th>
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
                                <td>
                                    <a href="?page=easyresy-manage&delete_id=<?php echo esc_attr($reservation->id); ?>" class="button">削除</a>
                                    <button type="button" class="button edit-button" data-id="<?php echo esc_attr($reservation->id); ?>">編集</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">予約が見つかりません。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 編集モーダル -->
        <div id="editModal" style="display:none;">
            <form method="POST" action="">
                <input type="hidden" name="edit_id" id="edit_id">
                <label for="edit_date">日付：</label>
                <input type="date" name="date" id="edit_date" required>
                <label for="edit_time_slot">時間帯：</label>
                <input type="text" name="time_slot" id="edit_time_slot" required>
                <label for="edit_name">名前：</label>
                <input type="text" name="name" id="edit_name" required>
                <label for="edit_phone">電話番号：</label>
                <input type="tel" name="phone" id="edit_phone" required>
                <label for="edit_email">メールアドレス：</label>
                <input type="email" name="email" id="edit_email" required>
                <label for="edit_notes">備考：</label>
                <textarea name="notes" id="edit_notes"></textarea>
                <button type="submit" class="button button-primary">保存</button>
                <button type="button" class="button close-modal">キャンセル</button>
            </form>
        </div>
    </div>

    <?php
    wp_enqueue_script('easyresy-manage-script', plugin_dir_url(__FILE__) . 'assets/js/manage-reservations.js', [], false, true);
}
?>
