<?php
if (!defined('ABSPATH')) exit;

$reservation_days = get_option('wp_reservation_days', []);
$time_slots = get_option('wp_reservation_time_slots', '');
$max_people = get_option('wp_reservation_max_people', 3);
?>

<div class="wrap">
    <h1>Reservation Settings</h1>
    <form method="POST" action="">
        <?php wp_nonce_field('save_wp_reservation_settings', 'wp_reservation_settings_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="reservation_days">Reservation Days</label></th>
                <td>
                    <label><input type="checkbox" name="reservation_days[]" value="Monday" <?php checked(in_array('Monday', $reservation_days)); ?>> Monday</label><br>
                    <label><input type="checkbox" name="reservation_days[]" value="Tuesday" <?php checked(in_array('Tuesday', $reservation_days)); ?>> Tuesday</label><br>
                    <label><input type="checkbox" name="reservation_days[]" value="Thursday" <?php checked(in_array('Thursday', $reservation_days)); ?>> Thursday</label>
                </td>
            </tr>
            <tr>
                <th><label for="time_slots">Time Slots</label></th>
                <td><input type="text" id="time_slots" name="time_slots" value="<?php echo esc_attr($time_slots); ?>" placeholder="e.g., 10:30-12:00,13:00-14:30"></td>
            </tr>
            <tr>
                <th><label for="max_people">Max People per Slot</label></th>
                <td><input type="number" id="max_people" name="max_people" value="<?php echo esc_attr($max_people); ?>" min="1"></td>
            </tr>
        </table>
        <p class="submit"><input type="submit" class="button-primary" value="Save Changes"></p>
    </form>
</div>
