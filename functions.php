<?php

function storefront_child_enqueue_styles()
{
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'storefront-child-style',
        get_stylesheet_directory_uri() . "/assets/css/style.css",
        ['storefront-style'],
        wp_get_theme()->get('Version')
    );
}

function storefront_child_register_custom_post_types()
{
    register_post_type('city', [
        'labels' => [
            'name' => 'Cities',
            'singular_name' => 'City'
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'cities'],
        'supports' => ['title'],
        // 'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'comments'],
        'menu_icon' => 'dashicons-building',
        'show_in_rest' => true
    ]);
}

function storefront_child_add_custom_fields_meta_box()
{
    add_meta_box(
        'city_coordinates',                      // ID of the meta box
        'City Coordinates',                      // Title of the meta box
        'storefront_child_city_coordinates_custom_fields_callback',       // Callback function to display the fields
        'city',                              // Post type to show this meta box on
        'normal',                            // Context (normal, side, advanced)
        'high'
    );

    // add_meta_box( $id:string, $title:string, $callback:callable, $screen:string|array|WP_Screen|null, $context:string, $priority:string, $callback_args:array|null )
}

function storefront_child_city_coordinates_custom_fields_callback($post)
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

function storefront_child_city_coordinates_custom_fields_save_post($post_id)
{
    // Checking if the field is set before saving
    if (isset($_POST['city_latitude'])) {
        update_post_meta($post_id, '_city_latitude', sanitize_text_field($_POST['city_latitude']));
    }

    if (isset($_POST['city_longitude'])) {
        update_post_meta($post_id, '_city_longitude', sanitize_text_field($_POST['city_longitude']));
    }
}

function storefront_child_register_taxonomy()
{
    $labels = [
        'name' => 'Country',
        'singular_name' => 'Country',
        'search_items' => 'Search Countries',
        'all_items' => 'All Countries',
        'edit_item' => 'Edit Country',
        'update_item' => 'Update Country',
        'add_new_item' => 'Add New Country',
        'new_item_name' => 'New Country Name',
        'menu_name' => 'Country'
    ];

    $args = [
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'country'],
        'show_in_rest' => true,
    ];

    register_taxonomy('country', 'city', $args);
}

define('THEME_FOLDER_PATH', trailingslashit(get_stylesheet_directory(__FILE__)));
require_once(THEME_FOLDER_PATH . 'widgets/class-city-with-weather-widget.php');

function storefront_child_register_widgets()
{
    register_widget('CityWithWeatherWidget');
}

function enqueue_ajax_search_script() {
    wp_enqueue_script('ajax-search', get_stylesheet_directory_uri() . '/assets/js/ajax-search.js', ['jquery'], null, true);

    wp_localize_script('ajax-search', 'ajax_search_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('ajax_search_nonce'),
    ]);
}

add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');
add_action('init', 'storefront_child_register_custom_post_types');
add_action('add_meta_boxes', 'storefront_child_add_custom_fields_meta_box');
add_action('save_post_city', 'storefront_child_city_coordinates_custom_fields_save_post');
add_action('init', 'storefront_child_register_taxonomy');
add_action('widgets_init', 'storefront_child_register_widgets');
add_action('wp_enqueue_scripts', 'enqueue_ajax_search_script');
add_action('wp_ajax_ajax_search_posts', 'handle_ajax_search_posts');
add_action('wp_ajax_nopriv_ajax_search_posts', 'handle_ajax_search_posts');




function fetch_city_temperature_in_celsius($latitude, $longitude = '', $cache_time = 600): string | bool
{
    $api_key = '65ec822f4ad2448ca94115722251505';
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

function handle_ajax_search_posts() {
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
