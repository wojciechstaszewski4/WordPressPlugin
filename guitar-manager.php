<?php

/*
 * Plugin Name:       Guitar Manager
 * Plugin URI:        https://github.com/wojciechstaszewski4
 * Description:       Wtyczka do zarządzania gitarami.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Wojciech Staszewski
 * Author URI:        https://www.linkedin.com/in/wojciechstaszewski/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/wojciechstaszewski4
 * Text Domain:       guitar-manager
 * Domain Path:       /languages
 */

// Dodawanie metaboxów:

add_action('add_meta_boxes', 'guitar_meta_box');

// Dodawanie metaboxu (Nazwa, tytuł, funkcja callback):

function guitar_meta_box()
{
    add_meta_box('guitar_meta', 'Guitar Info', 'guitar_meta');
}

// Funkcja wyświetlająca metabox dla danego postu:

function guitar_meta($post)
{
    $meta_type = get_post_meta($post->ID, 'guitar_type', true);
    $meta_quantity = get_post_meta($post->ID, 'guitar_quantity', true);
    $meta_price = get_post_meta($post->ID, 'guitar_price', true);

    echo '<h2>Typ:</h2>';

    $guitar_types = array('a' => 'Akustyczna', 'b' => 'Elektryczna', 'c' => 'Basowa', 'd' => 'Klasyczna');

    foreach ($guitar_types as $key => $type) {
        echo '<input type="radio" name="guitar_type" value="' . $key . '"';

        if ($meta_type == $key) {
            echo ' checked="checked"';
        }

        echo '>' . $type . '<br>';
    }

    echo '<h2>Ilość:</h2>';
    echo '<input type="number" name="guitar_quantity" value="' . esc_attr($meta_quantity) . '" /><br>';

    echo '<h2>Cena:</h2>';
    echo '<input type="text" name="guitar_price" value="' . esc_attr($meta_price) . '" /><br>';
}

// Zapisywanie danych z metaboxu:

function guitar_meta_save($post_id)
{
    if (isset($_POST['guitar_type'])) {
        update_post_meta($post_id, 'guitar_type', $_POST['guitar_type']);
    }

    if (isset($_POST['guitar_quantity'])) {
        update_post_meta($post_id, 'guitar_quantity', $_POST['guitar_quantity']);
    }

    if (isset($_POST['guitar_price'])) {
        update_post_meta($post_id, 'guitar_price', $_POST['guitar_price']);
    }
}

add_action('save_post', 'guitar_meta_save');

// Dodawanie panelu admina:

add_action('admin_menu', 'guitar_menu');

function guitar_menu()
{
    add_menu_page(
        'Guitar Manager', // Nazwa wtyczki wyświetlana w panelu admina.
        'Guitar Manager',
        'manage_options',
        'guitar_settings', // Funkcja wywoływana po wejściu do panelu wtyczki.
        'guitar_settings'
    );
}

// Dodawanie zawartości panelu wtyczki:

function guitar_settings()
{
    global $wpdb;
    $prefix = $wpdb->base_prefix;

    // Wyświetlanie htmla:

    echo '
    <h1>Guitar Panel</h1>
    <p>Dodaj gitary:</p>
    <form method="post" action="#">
        <input type="text" name="guitar_name" placeholder="Nazwa"><br>
        <input type="number" name="guitar_quantity" placeholder="Ilość"><br>
        <input type="text" name="guitar_price" placeholder="Cena"><br><br>
        <input type="submit" value="Dodaj">
    </form>
    ';

    if (isset($_POST['guitar_name']) && $_POST['guitar_name'] != '' && isset($_POST['guitar_quantity']) && $_POST['guitar_quantity'] != '' && isset($_POST['guitar_price']) && $_POST['guitar_price'] != '') {
        $name = $_POST['guitar_name'];
        $quantity = $_POST['guitar_quantity'];
        $price = $_POST['guitar_price'];
        $wpdb->query("INSERT INTO " . $prefix . "guitars (name, quantity, price) VALUES ('$name', '$quantity', '$price')");
    }

    echo '<ul>';
    $results = $wpdb->get_results("SELECT * FROM " . $prefix . "guitars", ARRAY_A);
    echo '<p>Lista gitar:</p>';

    foreach ($results as $item) echo '<li>Nazwa: ' . $item['name'] . ' | Ilość: ' . $item['quantity'] . ' | Cena: ' . $item['price'] . 'zł/szt' . '</li>';

    echo '</ul>';
}

// Tworzenie tabeli przy aktywacji wtyczki:

register_activation_hook(__FILE__, 'guitar_activation');

function guitar_activation()
{
    global $wpdb;
    $prefix = $wpdb->base_prefix;
    $wpdb->query("CREATE TABLE " . $prefix . "guitars (`id` INT NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `quantity` INT NOT NULL, `price` TEXT NOT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;");
}

// Usuwanie tabeli przy dezaktywacji wtyczki:

register_deactivation_hook(__FILE__, 'guitar_deactivation');

function guitar_deactivation()
{
    global $wpdb;
    $prefix = $wpdb->base_prefix;
    $wpdb->query("DROP TABLE " . $prefix . "guitars");
}

// Shortcode wyświetlający listę numerowaną z popularnymi modelami gitar:

function guitar_list($args)
{
    $sum = intval($args['sum']);

    $guitar_models = array(
        'Fender Stratocaster',
        'Gibson Les Paul',
        'Ibanez RG',
        'PRS Custom 24',
        'Yamaha Pacifica',
        'Jackson Dinky',
        'ESP Eclipse',
        'Epiphone SG',
        'Schecter Hellraiser',
        'Gretsch Electromatic'
    );

    $html = '<h3>Popularne modele gitar</h3>';
    $html .= '<ul>';

    for ($i = 0; $i < $sum && $i < count($guitar_models); $i++) {
        $html .= '<li><strong>' . ($i + 1) . ' - </strong>' . $guitar_models[$i] . '</li>';
    }

    if ($sum > count($guitar_models)) {
        for ($i = count($guitar_models); $i < $sum; $i++) {
            $html .= '<li><strong>' . ($i + 1) . ' - </strong> Inny model gitary</li>';
        }
    }

    $html .= '</ul>';
    return $html;
}


add_shortcode('guitar_list', 'guitar_list');

// Shortcode wyświetlający dane z bazy danych:

function display_guitars($args)
{
    global $wpdb;

    $prefix = $wpdb->base_prefix;
    $results = $wpdb->get_results("SELECT * FROM " . $prefix . "guitars", ARRAY_A);
    $html = '<table border="5" cellspacing="10" cellpadding="5"><tr><th>Nazwa:</th><th>Ilość:</th><th>Cena (zł/szt):</th></tr>';

    foreach ($results as $item) {
        $html .= '<tr><td>' . $item['name'] . '</td><td>' . $item['quantity'] . '</td><td>' . $item['price'] . '</td></tr>';
    }

    $html .= '</table>';
    return $html;
}

add_shortcode('display_guitars', 'display_guitars');

// Shortcode wyświetlający informacje o gitarze z metaboxu:

function display_guitar_info($atts)
{
    global $post;

    $type = get_post_meta($post->ID, 'guitar_type', true);
    $quantity = get_post_meta($post->ID, 'guitar_quantity', true);
    $price = get_post_meta($post->ID, 'guitar_price', true);

    $guitar_types = array(
        'a' => 'Akustyczna',
        'b' => 'Elektryczna',
        'c' => 'Basowa',
        'd' => 'Klasyczna'
    );

    $type_name = isset($guitar_types[$type]) ? $guitar_types[$type] : 'Nieznany typ';

    $html = '<div class="guitar-info">';
    $html .= '<h3>Informacje o gitarze:</h3>';
    $html .= '<p><strong>Typ:</strong> ' . esc_html($type_name) . '</p>';
    $html .= '<p><strong>Ilość:</strong> ' . esc_html($quantity) . '</p>';
    $html .= '<p><strong>Cena:</strong> ' . esc_html($price) . 'zł/szt' . '</p>';
    $html .= '</div>';

    return $html;
}

add_shortcode('guitar_info', 'display_guitar_info');
