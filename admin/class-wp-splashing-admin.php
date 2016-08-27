<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://studioepresso.co
 * @since      1.0.0
 *
 * @package    Wp_Splashing
 * @subpackage Wp_Splashing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Splashing
 * @subpackage Wp_Splashing/admin
 * @author     Studio Espresso <jan@studioespresso.co>
 */
class Wp_Splashing_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->unsplash = new Wp_Splashing_Unsplash($this->plugin_name, $this->version);

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-splashing-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name . '-spin', plugin_dir_url( __FILE__ ) . 'js/wp-splashing-loadingoverlay.js', array( 'jquery' ), $this->version, false );

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-splashing-admin.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'wp_splashing_settings', array(
                'ajax_admin_url' => admin_url( 'admin-ajax.php' ),
                'wp_splashing_admin_nonce' => wp_create_nonce( 'wp_splashing_nonce' )
            ));

    }

    public function wp_splashing_settings_page() { 
        require('partials/wp-splashing-admin-display.php');   
    }

    public function wp_splashing_search() {

        $this->checkNonce($_POST["nonce"]);
        
        $string = sanitize_text_field($_POST['data']);
        $data = $this->unsplash->search($string);
        foreach($data as $image) {
            $images[] = $image;
            break;
        }
        echo json_encode($images);
    }

    public function wp_splashing_save_image() {

        $this->checkNonce($_POST["nonce"]);

        $dir = plugin_dir_path( dirname( __FILE__ ) ) . 'temp/';
        if(!is_dir($dir)){
            mkdir($dir);
        }

        if (!is_writable(plugin_dir_path( dirname( __FILE__ ) ) . 'temp/')) {
          echo __('Unable to save image, check your server permissions.', 'wp-splashing');
        }

        $payload = Trim(stripslashes($_POST['image']));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $payload);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $picture = curl_exec($ch);
        curl_close($ch);

        $tmpImage = 'photo-' . rand() . '.jpg';
        $tmp = $dir . $tmpImage;

        $saved_file = file_put_contents($tmp, $picture);

        $json = json_encode(
            array(
                'error' => true,
                'msg' => __('Unable to save image, check your server permissions.', USP_NAME)
            )
        );

        // Was the temporary image able to be saved?
        if ($saved_file) {
            $uploadPath = plugins_url('temp/', dirname(__FILE__));
            $file =  $uploadPath . $tmpImage;
            // Upload generated file to media library using media_sideload_image()
            $splashingImage = media_sideload_image( $file , null, 'blaa' );

            // Success JSON
            //echo __('File successfully uploaded to media library.', USP_NAME);
            $json = json_encode(
                array(
                    'error' => false,
                    'msg' => __('File successfully uploaded to media library.', USP_NAME)
                )
            );

            // Delete the file we just uplaoded from the tmp dir.
            if(file_exists($tmp_path.''.$tmp)){
                unlink($tmp_path.''.$tmp);
            }else{
                echo __('Nothing to delete, file does not exist', USP_NAME);
            }
        }

        echo $json;
        die();
    }

    public function wp_splashing_add_menu() {
        add_submenu_page( 'upload.php', 'Splashing Images', 'Splashing Images', 'upload_files', 'wp-splashing', array( $this, 'wp_splashing_settings_page' ));
    }

    public function checkNonce($nonce) {
        // Check our nonce, if they don't match then bounce!
        if (! wp_verify_nonce( $nonce, 'wp_splashing_nonce' )) {
            die('Get Bounced!');
        }
    }

}
