<?php
class CityWithWeatherWidget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'city_with_weather_widget',
            'City With Weather Widget',
            [
                'description' => 'Shows the temperature of a city selected',
            ],
        );
    }

    // Showing the widget in the frontend
    public function widget($args, $instance): void
    {
        echo $args['before_widget'];

        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : 0;


        if ($city_id) {
            $city = get_post($city_id);
            $latitude = get_post_meta($city->ID, '_city_latitude', true);
            $longitude = get_post_meta($city->ID, '_city_longitude', true);

            if ($city) {
                $city_name = $city->post_title;

                $temperature = fetch_city_temperature_in_celsius($latitude, $longitude);

                echo "<h3>{$city_name}</h3>";
                if ($temperature !== false) {
                    echo "<p>Temperature: {$temperature} °C</p>";
                } else {
                    echo "<p>Error fetching temperature</p>";
                }
            }
        }

        echo $args['after_widget'];
    }

    public function form($instance): void
    {
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : 0;

        // Getting all the city posts
        $cities = get_posts([
            'post_type' => 'city',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

?>

        <p>
            <label for="<?php echo $this->get_field_id('city_id'); ?>">Select a city:</label>
            <select id="<?php echo $this->get_field_id('city_id');  ?>"
                name="<?php echo $this->get_field_name('city_id'); ?>">
                <option value="">-- Select a city --</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo $city->ID; ?>" <?php selected($city_id, $city->ID); ?>>
                        <?php echo esc_html($city->post_title);  ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

<?php
    }

    public function update($new_instance, $old_instance): array
    {
        $instance = [];
        $instance['city_id'] = !empty($new_instance['city_id']) ? intval($new_instance['city_id']) : 0;
        return $instance;
    }
}
