<?php

require_once get_stylesheet_directory() . '/inc/utils.php';

function storefront_child_enqueue_styles(): void
{
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'storefront-child-style',
        get_stylesheet_directory_uri() . "/assets/css/style.css",
        ['storefront-style'],
        wp_get_theme()->get('Version')
    );
}

function storefront_child_register_custom_post_types(): void
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

function storefront_child_add_custom_fields_meta_boxes(): void
{
    add_meta_box(
        'city_coordinates',
        'City Coordinates',
        'storefront_child_city_coordinates_custom_fields_callback',       // Callback function to display the fields
        'city',                           
        'normal',
        'high',
    );
}

function storefront_child_register_taxonomies(): void
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

function storefront_child_register_widgets(): void
{
    register_widget('CityWithWeatherWidget');
}

function storefront_child_enqueue_scripts(): void
{
    wp_enqueue_script('ajax-search', get_stylesheet_directory_uri() . '/assets/js/ajax-search.js', ['jquery'], null, true);

    wp_localize_script('ajax-search', 'ajax_search_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('ajax_search_nonce'),
    ]);
}

add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');
add_action('init', 'storefront_child_register_custom_post_types');
add_action('add_meta_boxes', 'storefront_child_add_custom_fields_meta_boxes');
add_action('save_post_city', 'storefront_child_city_coordinates_custom_fields_save_post');
add_action('init', 'storefront_child_register_taxonomies');
add_action('widgets_init', 'storefront_child_register_widgets');
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_scripts');
add_action('wp_ajax_ajax_search_posts', 'handle_ajax_search_posts');
add_action('wp_ajax_nopriv_ajax_search_posts', 'handle_ajax_search_posts');

