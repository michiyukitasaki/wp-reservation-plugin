<?php
if (!defined('ABSPATH')) exit;

// Reservations page callback
function wp_reservation_admin_reservations_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    $reservations = $wpdb->get_results("SELECT * FROM $table_name");

    ?>
    <div class="wrap">
        <h1>Reservation Management</h1>
        <form method="GET" action="">
            <input type="hidden" name="page" value="wp-reservation-settings">
            <input type="text" name="filter_date" placeholder="YYYY-MM-DD" value="<?php echo isset($_GET['filter_date']) ? esc_attr($_GET['filter_date']) : ''; ?>">
            <input type="text" name="filter_name" placeholder="Customer Name" value="<?php echo isset($_GET['filter_name']) ? esc_attr($_GET['filter_name']) : ''; ?>">
            <button type="submit" class="button">Filter</button>
        </form>
        <table class="widefat fixed" cellspacing="0">
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
            </tbody>
        </table>
    </div>
    <?php
}
?>
