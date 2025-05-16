<?php

/**
 * Template Name: Cities And Countries Page Template
 */

get_header() ?>

<h3><?php the_title() ?></h3>

<?php

function get_cities()
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    return $wpdb->get_results(
        "SELECT p.post_title AS city_name, t.name AS country_name, lat_meta.meta_value AS city_latitude, lng_meta.meta_value AS city_longitude  
        FROM {$prefix}posts p
        LEFT JOIN {$prefix}term_relationships AS tr ON p.ID = tr.object_id
        LEFT JOIN {$prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'country'
        LEFT JOIN {$prefix}terms AS t ON tt.term_id = t.term_id
        INNER JOIN {$prefix}postmeta AS lat_meta ON p.ID = lat_meta.post_id AND lat_meta.meta_key = '_city_latitude'
        INNER JOIN {$prefix}postmeta AS lng_meta ON p.ID = lng_meta.post_id AND lng_meta.meta_key = '_city_longitude'
            WHERE p.post_status = 'publish' 
                AND p.post_type='city'",
        OBJECT
    );
}
?>

<input type="text" id="ajax-search" placeholder="Search posts..." />
<div id="ajax-search-results"></div>

<?php do_action('storefront_child_before_cities_and_countries_table'); ?>

<table>
    <caption>City Weather Data</caption>
    <thead>
        <tr>
            <th>City Name</th>
            <th>Country</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Temperature (°C)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach(get_cities() as $city): ?>
            <tr>
                <td><?php echo $city->city_name ?></td>
                <td><?php echo $city->country_name ?></td>
                <td><?php echo $city->city_latitude ?></td>
                <td><?php echo $city->city_longitude ?></td>
                <td><?php echo fetch_city_temperature_in_celsius($city->city_latitude, $city->city_longitude); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php do_action('storefront_child_after_cities_and_countries_table'); ?>

<?php get_footer() ?>