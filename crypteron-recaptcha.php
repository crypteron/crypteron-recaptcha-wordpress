<?php
/*
Plugin Name: Crypteron Recaptcha
Plugin URI: https://www.crypteron.com
Description: Requires Google Recaptcha 2.0 for login
Author: Yaron Guez
Author URI: http://yaronguez.com
License: GPL2
GitHub Plugin URI: https://github.com/crypteron/crypteron-recaptcha-wordpress
GitHub Branch: master
*/


class Crypteron_Recaptcha
{
    protected static $instance = null;

    private function __construct()
    {
        // Load settings
        $this->server_key = get_option('crypteron_recaptcha_server_key');
        $this->client_key = get_option('crypteron_recaptcha_client_key');

        // If settings have been provided, hook into login process
        if($this->server_key && $this->client_key) {
            add_action('login_form', array($this, 'display_recaptcha'));
            add_action('lostpassword_form', array($this, 'display_recaptcha'));
            add_filter('wp_authenticate_user', array($this, 'verify_captcha'));
            add_filter('allow_password_reset', array($this, 'verify_captcha'));
            add_action('login_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        // Load settings page
        if ( is_admin() ){
            add_action( 'admin_menu', array($this, 'add_recaptcha_menu' ));
            add_action( 'admin_init', array($this, 'register_recaptcha_settings' ));
        }

    }

    public function add_recaptcha_menu(){
        // This page will be under "Settings"
        add_options_page(
            'Crypteron reCAPTCHA Settings',
            'reCAPTCHA',
            'manage_options',
            'crypteron-recaptcha',
            array( $this, 'settings_page' )
        );
    }

    public function register_recaptcha_settings(){
        register_setting('crypteron-recaptcha-group','crypteron_recaptcha_client_key', array($this, 'sanitize'));
        register_setting('crypteron-recaptcha-group','crypteron_recaptcha_server_key', array($this, 'sanitize'));
    }

    public function settings_page(){
        include(plugin_dir_path( __FILE__).'settings.php');
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        return sanitize_text_field($input);
    }


    public function enqueue_scripts()
    {
        wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );
        wp_enqueue_script( 'recaptcha-display-buttons', plugin_dir_url(__FILE__) . 'main.js', array('jquery'));
        wp_enqueue_style('recaptcha-hide-buttons', plugin_dir_url(__FILE__) . 'main.css');
    }

    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    public function display_recaptcha()
    {
        echo '<div class="g-recaptcha" data-sitekey="' . $this->client_key . '" data-callback="recaptcha_callback"></div>';
    }

    public function verify_captcha( $user)
    {
        if (isset($_POST['g-recaptcha-response'])) {
            $response = json_decode(
                wp_remote_retrieve_body(
                    wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=$this->server_key&response=" . $_POST['g-recaptcha-response'])), true);

            if ($response["success"]) {
                return $user;
            }
        }
        return new WP_Error('recaptcha_fail', 'reCAPTCHA check failed');
    }

}

Crypteron_Recaptcha::get_instance();