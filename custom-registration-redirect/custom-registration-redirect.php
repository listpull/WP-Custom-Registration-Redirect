<?php
/**
 * Plugin Name: Custom Registration Redirect
 * Description: Allows specifying a custom redirect URL after user registration.
 * Version: 1.0
 */

// Add a settings page
add_action('admin_menu', 'custom_registration_redirect_menu');
function custom_registration_redirect_menu() {
    add_options_page('Registration Redirect Settings', 'Registration Redirect', 'manage_options', 'custom-registration-redirect-settings', 'custom_registration_redirect_settings_page');
}

// Render the settings page
function custom_registration_redirect_settings_page() {
    ?>
    <div class="wrap">
        <h2>Registration Redirect Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('custom_registration_redirect_options'); ?>
            <?php do_settings_sections('custom-registration-redirect-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register plugin settings
add_action('admin_init', 'custom_registration_redirect_admin_init');
function custom_registration_redirect_admin_init() {
    register_setting('custom_registration_redirect_options', 'custom_registration_redirect_settings');
    add_settings_section('custom_registration_redirect_section', 'Redirect Settings', 'custom_registration_redirect_section_callback', 'custom-registration-redirect-settings');
    add_settings_field('redirect_url', 'Redirect URL', 'custom_registration_redirect_url_callback', 'custom-registration-redirect-settings', 'custom_registration_redirect_section');
}

// Section callback
function custom_registration_redirect_section_callback() {
    echo 'Specify the URL where users will be redirected after registration.';
}

// Field callback
function custom_registration_redirect_url_callback() {
    $options = get_option('custom_registration_redirect_settings');
    $redirect_url = isset($options['redirect_url']) ? $options['redirect_url'] : '';
    echo '<input type="text" name="custom_registration_redirect_settings[redirect_url]" value="' . esc_attr($redirect_url) . '" />';
}



// Auto login after registration
function auto_login_after_registration($user_id) {
    $user = get_userdata($user_id);
    $user_login = $user->user_login;

    wp_set_auth_cookie($user_id);
    do_action('wp_login', $user_login, $user);

    $options = get_option('custom_registration_redirect_settings');
    $redirect_url = isset($options['redirect_url']) ? $options['redirect_url'] : '';
    wp_redirect($redirect_url ? esc_url_raw($redirect_url) : $redirect_to);
    exit;
}

add_action('user_register', 'auto_login_after_registration');
