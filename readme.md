# Storefront Child Theme

A custom WordPress child theme built on the Storefront parent theme, adding city-based weather functionality and custom post types.

## Features

- Custom 'City' post type with weather integration
- City coordinates metabox for latitude and longitude
- Country taxonomy for categorizing cities
- Custom widget displaying cities with current weather information
- AJAX-powered search functionality for cities
- Weather data fetching from WeatherAPI.com

## Installation

1. Install and activate the Storefront theme (parent theme)
2. Upload the `storefront-child` folder to your `/wp-content/themes/` directory
3. Activate the Storefront Child theme through the WordPress admin panel

## Configuration

### Weather API Key

The theme uses WeatherAPI.com for weather data. A default API key is included, but you may want to replace it with your own:

1. Get your API key from [WeatherAPI.com](https://www.weatherapi.com/)
2. Update the `WEATHER_API_KEY` constant in `inc/utils.php`

## Custom Post Types and Taxonomies

### Cities

The theme includes a custom 'City' post type with:
- Custom metabox for coordinates (latitude/longitude)
- 'Country' taxonomy for categorization
- Weather data integration

### Custom Widget

The theme includes a custom widget 'City with Weather' that displays:
- Selected city name
- Current temperature from WeatherAPI
- Optional custom title

## Directory Structure

```
storefront-child/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── ajax-search.js
├── inc/
│   └── utils.php
├── widgets/
│   └── class-city-with-weather-widget.php
├── functions.php
└── style.css
```

## Custom Functions

### Weather Data

The theme includes utility functions for fetching weather data:

- `fetch_city_temperature_in_celsius()` - Gets current temperature for coordinates
  - Includes caching for improved performance (10 minute default)

### AJAX Search

The theme includes AJAX-powered search for the Cities custom post type:

- Live search results as you type
- Direct links to city pages

## Development

### Adding New Features

1. For new post types, add registration code to `functions.php` 
2. For new utilities, add functions to `inc/utils.php`
3. For new widgets, create class in `widgets/` directory and register in `functions.php`

### CSS Customization

The theme enqueues its styles from `assets/css/style.css` which overrides parent theme styles.