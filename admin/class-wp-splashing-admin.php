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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-splashing-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'wp_splashing_settings', array(
	        	'ajax_admin_url' => admin_url( 'admin-ajax.php' ),
				'wp_splashing_admin_nonce' => wp_create_nonce( 'wp_splashing_nonce' )
			));

	}

	public function wp_splashing_settings_page() { ?>
			<div class="wrap">
				<h1><?php _e('Splashing Images', 'wp-splashing')?></h1>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div class="metabox-holder columns-2">
							<div id="splashing_images" style="position: relative;" class="postbox-container">
							<div class="media-toolbar wp-filter"><div class="media-toolbar-secondary"><div class="view-switch media-grid-view-switch">
									<a href="/wp-admin/upload.php?mode=list" class="view-list">
										<span class="screen-reader-text">List View</span>
									</a>
									<a href="/wp-admin/upload.php?mode=grid" class="view-grid current">
										<span class="screen-reader-text">Grid View</span>
									</a>
								</div>
								<form id="splashing-search" method="get">
									<label class="screen-reader-text" for="post-search-input">Search Posts:</label>
									<input type="search" id="post-search-input" name="s" value="">
									<input type="submit" id="search-submit" class="button" value="Search Unsplash">
								</form>	
								</div>
							</div>
								<?php 
                    $images = $this->unsplash->getLastFeatured(50);
                    foreach($images as $image) {
                        echo '<a href="" class="upload" data-source="' . $image->links['download'] . '"><img class="splashing-thumbnail" src="' . $image->urls['thumb'] .'"></a>';
                    }
                    ?>
							</div>
							<div id="postbox-container-1" class="postbox-container">
								<div class="postbox">
									<h2 class="hndle"><?php _e('Powered by Unsplash', 'wp-splashing'); ?></h2>
									<div class="inside">
										<p><?php _e('Splashing Images is powered by <a href="http://unsplash.com">unsplash.com</a> and the Unsplash API.', 'wp-splashing'); ?></p>
										<h3><?php _e('Unsplash License ', 'wp-splashing'); ?></h3>
										<p><?php _e('All photos published on Unsplash are licensed under Creative Commons Zero which means you can copy, modify, distribute and use the photos for free, including commercial purposes, without asking permission from or providing attribution to the photographer or Unsplash.', 'wp-splashing'); ?></p>
									</<div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
	}

	public function wp_splashing_search() {
		return var_dump($_POST);
		
	}

	public function wp_splashing_save_image() {

	   	$nonce = $_POST["nonce"];
   		// Check our nonce, if they don't match then bounce!
   		if (! wp_verify_nonce( $nonce, 'wp_splashing_nonce' )) {
	   		die('Get Bounced!');
   		}

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

}
