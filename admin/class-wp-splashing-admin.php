<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://studioespresso.co
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
        wp_enqueue_script( $this->plugin_name . '-imagesloaded', plugin_dir_url( __FILE__ ) . 'js/imagesloaded.pkgd.min.js', array('jquery'), $this->version, false );
        wp_enqueue_script( $this->plugin_name . '-masonry', plugin_dir_url( __FILE__ ) . 'js/masonry.pkgd.min.js', array('jquery'), $this->version, false );

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-splashing-admin.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'wp_splashing_settings', array(
                'ajax_admin_url' => admin_url( 'admin-ajax.php' ),
                'wp_splashing_admin_nonce' => wp_create_nonce( 'wp_splashing_nonce' )
            ));

    }

    public function wp_splashing_settings_page() {
        if($_GET['disconnect']) {
            update_option('splashing_access_token', null);
        }
        if (version_compare(phpversion(), "5.5.0", ">=")) {
            // you're on 5.5.0 or later
            require('partials/wp-splashing-admin-main.php');
        } else {
            echo "<div class='wrap'></div><h1>" . __('Splashing Images', 'wp-splashing-images') . " <span style='filter: grayscale(100%);'>&#128247; </span></h1>";
            echo "<p>" . __('Looks like your server\'s version of PHP is too old to run this plugin.</p><p>Splashing Images requires PHP 5.5 or higher. If you have any questions, feel free to <a href="mailto:support@studioespresso.co">get in touch</a> and we\'ll try to help you out in any way we can.</p>' , 'wp-splashing-images') . "</p></div>";
        }
    }

    public function wp_splashing_search() {
        $this->checkNonce($_GET["nonce"]);
        $string = sanitize_text_field($_GET['search']);
        $page = sanitize_text_field($_GET['paged']);
        wp_redirect( '/wp-admin/upload.php?page=wp-splashing&search=' . $string . '&paged=' . $page, 302 );
    }

    public function wp_splashing_save_image() {

        $this->checkNonce($_POST["nonce"]);
        $dir = plugin_dir_path( dirname( __FILE__ ) ) . 'temp/';
        if(!is_dir($dir)){ mkdir($dir); }

        if (!is_writable(plugin_dir_path( dirname( __FILE__ ) ) . 'temp/')) {
          echo __('Unable to save image, check your server permissions.', 'wp-splashing');
        }

        $payload = trim(stripslashes($_POST['image']));
        $author = $_POST['author'];

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
                'msg' => __('Unable to save image, check your server permissions.', 'wp-splashing')
            )
        );

        // Was the temporary image able to be saved?
        if ($saved_file) {
            $uploadPath = plugins_url('temp/', dirname(__FILE__));
            $file =  $uploadPath . $tmpImage;
	        $credit = __('Photo by ', 'wp-splashing') . $author;
            $splashingImage = media_sideload_image( $file , null, $credit );

            if($splashingImage instanceof WP_Error) {
                $json = json_encode(
                    array(
                        'error' => false,
                        'msg' => __('Something went wrong saving the image.', 'wp-splashing')
                    )
                );
                echo $json;
            }

            // Success JSON
            //echo __('File successfully uploaded to media library.', USP_NAME);
            $json = json_encode(
                array(
                    'error' => false,
                    'msg' => __('File successfully uploaded to media library.', 'wp-splashing')
                )
            );

            // Delete the file we just uplaoded from the tmp dir.
            if(file_exists($dir.''.$tmpImage)){
                unlink($dir.''.$tmpImage);
            }else{
                echo __('Nothing to delete, file does not exist', 'wp-splashing');
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
