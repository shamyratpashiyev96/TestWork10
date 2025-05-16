<?php
get_header();
?>

<?php
$city = get_post();

$latitude = get_post_meta($city->ID, '_city_latitude', true);
$longitude = get_post_meta($city->ID, '_city_longitude', true);
$taxonomies = get_the_terms($city->ID, 'country');
$country_name = $taxonomies[0]->name ?? 'No country'

?>

<p>City Name: <?php echo $city->post_title ?></p>
<p>Country: <?php echo $country_name ?></p>
<p>Latitude: <?php echo $latitude ?></p>
<p>Longitude: <?php echo $longitude ?></p>
<p>Temperature (°C): <?php echo fetch_city_temperature_in_celsius($latitude, $longitude); ?></p>

<?php get_footer() ?>