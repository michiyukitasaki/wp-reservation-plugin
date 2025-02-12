<?php
if (!defined('ABSPATH')) exit;

$reservation_days = get_option('wp_reservation_days', []);
?>

<div id="wp-reservation-calendar-container">
    <h2>Select a Date</h2>
    <div id="wp-reservation-calendar"></div>
</div>

<?php
wp_enqueue_script('wp-reservation-calendar-script', plugin_dir_url(__FILE__) . '../assets/js/calendar.js', [], false, true);
wp_enqueue_style('wp-reservation-calendar-style', plugin_dir_url(__FILE__) . '../assets/css/calendar.css');
?>
