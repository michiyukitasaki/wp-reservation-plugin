<?php
if (!defined('ABSPATH')) exit;

// Reservations page callback
function wp_reservation_admin_reservations_page() {
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
        echo '<script>window.location.reload();</script>'; // ページをリロード
    }

    ?>
    <div class="wrap">
        <h1>予約管理</h1>
        <div class="filter-box">
            <form method="GET" action="">
                <input type="hidden" name="page" value="wp-reservation-manage">
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
                    <input type="text" id="filter_name" name="filter_name" placeholder="名前" value="<?php echo esc_attr($filter_name); ?>">
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
                                    <a href="?page=wp-reservation-manage&delete_id=<?php echo esc_attr($reservation->id); ?>" class="button">削除</a>
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
        <div id="editModal" style="display:none; z-index: 9999;">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-button');
            const editModal = document.getElementById('editModal');
            const closeModalButtons = document.querySelectorAll('.close-modal');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const row = this.closest('tr');
                    const date = row.children[1].textContent;
                    const timeSlot = row.children[2].textContent;
                    const name = row.children[3].textContent;
                    const phone = row.children[4].textContent;
                    const email = row.children[5].textContent;
                    const notes = row.children[6].textContent;

                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_date').value = date;
                    document.getElementById('edit_time_slot').value = timeSlot;
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_phone').value = phone;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_notes').value = notes;

                    editModal.style.display = 'block';
                });
            });

            closeModalButtons.forEach(button => {
                button.addEventListener('click', function () {
                    editModal.style.display = 'none';
                });
            });
        });
    </script>
    <?php
}
?>
