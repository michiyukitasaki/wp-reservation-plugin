<?php
if (!defined('ABSPATH')) exit; // WordPress の安全確認

// 設定ページの表示関数
function easyresy_settings_page() {
    // POST リクエストを処理して設定を保存
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['easyresy_reservation_settings_nonce'])) {
        if (wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['easyresy_reservation_settings_nonce'])), 'save_easyresy_reservation_settings')) {
            // 各設定項目を保存
            update_option('easyresy_reservation_days', isset($_POST['reservation_days']) ? array_map('sanitize_text_field', wp_unslash($_POST['reservation_days'])) : []);
            update_option('easyresy_reservation_time_slots', isset($_POST['time_slots']) ? sanitize_text_field(wp_unslash($_POST['time_slots'])) : '');
            update_option('easyresy_reservation_max_people', isset($_POST['max_people']) ? intval(wp_unslash($_POST['max_people'])) : 3);
            update_option('easyresy_reservation_theme_color', isset($_POST['theme_color']) ? sanitize_hex_color(wp_unslash($_POST['theme_color'])) : '#8B4513');

            // 成功メッセージ
            echo '<div class="updated"><p>Settings saved successfully!</p></div>';
        }
    }

    // 現在の設定値を取得
    $reservation_days = get_option('easyresy_reservation_days', []);
    $time_slots = get_option('easyresy_reservation_time_slots', '');
    $max_people = get_option('easyresy_reservation_max_people', 3);
    $theme_color = get_option('easyresy_reservation_theme_color', '#8B4513');

    ?>
    <div class="wrap">
        <h1>予約システムの設定</h1>
        <form method="POST">
            <?php wp_nonce_field('save_easyresy_reservation_settings', 'easyresy_reservation_settings_nonce'); ?>
            <table class="form-table">
                <!-- 予約可能な曜日 -->
                <tr>
                    <th><label for="reservation_days">予約可能な曜日</label></th>
                    <td>
                        <?php
                        $all_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($all_days as $day) {
                            echo '<label><input type="checkbox" name="reservation_days[]" value="' . esc_attr($day) . '" ' . checked(in_array($day, $reservation_days), true, false) . '> ' . esc_html($day) . '</label><br>';
                        }
                        ?>
                    </td>
                </tr>

                <!-- 予約可能な時間帯 -->
                <tr>
                    <th><label for="time_slots">予約可能な時間帯</label></th>
                    <td>
                        <input type="text" id="time_slots" name="time_slots" value="<?php echo esc_attr($time_slots); ?>" placeholder="e.g., 10:30-12:00,13:30-15:00">
                        <p class="description">例のような表記方法で入力してください (例： 10:30-12:00,13:30-15:00).</p>
                    </td>
                </tr>

                <!-- 最大人数 -->
                <tr>
                    <th><label for="max_people">1枠あたりの最大予約可能人数</label></th>
                    <td>
                        <input type="number" id="max_people" name="max_people" value="<?php echo esc_attr($max_people); ?>" min="1">
                        <p class="description">1枠あたりの最大予約可能人数を例のように入力してください（例：3）</p>
                    </td>
                </tr>

                <!-- テーマカラー -->
                <tr>
                    <th><label for="theme_color">テーマカラー</label></th>
                    <td>
                        <input type="color" id="theme_color" name="theme_color" value="<?php echo esc_attr($theme_color); ?>">
                        <p class="description">テーマカラーを選択してください。</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="設定を保存">
            </p>
        </form>
    </div>
    <?php
}
?>
