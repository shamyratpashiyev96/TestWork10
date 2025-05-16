<?php

define('WEATHER_API_KEY', '65ec822f4ad2448ca94115722251505');

function fetch_city_temperature_in_celsius($latitude, $longitude = '', $cache_time = 600): string | bool
{
    $api_key = '';
    if (defined('WEATHER_API_KEY')) {
        $api_key = WEATHER_API_KEY;
    }

    $q = "{$latitude},{$longitude}";
    $get_air_quality = false;
    $get_air_quality_string = $get_air_quality ? 'yes' : 'no';
    $url = "http://api.weatherapi.com/v1/current.json?key={$api_key}&q={$q}&aqi={$get_air_quality_string}";
    $cache_key = $q;

    // Getting from cache
    if ($cache_key) {
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }
    }

    // Making an API request
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['current']['temp_c'])) {
        $result = $data['current']['temp_c'];
        set_transient($cache_key, $result, $cache_time); // cache_time in seconds
        return $result;
    }


    return false;
}

function handle_ajax_search_posts(): void
{
    check_ajax_referer('ajax_search_nonce', 'nonce');

    $keyword = sanitize_text_field($_POST['keyword']);

    $args = [
        's' => $keyword,
        'post_type' => 'city',
        'post_status' => 'publish',
        'posts_per_page' => 5
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No results found.</p>';
    }

    wp_die();
}

// Displaying metadata update form
function storefront_child_city_coordinates_custom_fields_callback($post): void
{
    $latitude = get_post_meta($post->ID, '_city_latitude', true);
    $longitude = get_post_meta($post->ID, '_city_longitude', true);

?>
    <label for="city_latitude">Latitude:</label>
    <input type="text" name="city_latitude" id="city_latitude" value="<?php echo esc_attr($latitude); ?>" style="width:100%;" />

    <label for="city_longitude">Longitude:</label>
    <input type="text" name="city_longitude" id="city_longitude" value="<?php echo esc_attr($longitude); ?>" style="width:100%;" />

<?php
}

// Metadata updating callback
function storefront_child_city_coordinates_custom_fields_save_post($post_id): void
{
    // Checking if the field is set before saving
    if (isset($_POST['city_latitude'])) {
        update_post_meta($post_id, '_city_latitude', sanitize_text_field($_POST['city_latitude']));
    }

    if (isset($_POST['city_longitude'])) {
        update_post_meta($post_id, '_city_longitude', sanitize_text_field($_POST['city_longitude']));
    }
}
