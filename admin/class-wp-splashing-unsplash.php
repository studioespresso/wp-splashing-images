<?php

/**
 * The unsplash-specific functionality of the plugin.
 *
 * @link       http://studioepresso.co
 * @since      1.0.0
 *
 * @package    Wp_Splashing
 * @subpackage Wp_Splashing/admin
 */

/**
 * The unsplash-specific functionality of the plugin.
 *
 * @package    Wp_Splashing
 * @subpackage Wp_Splashing/admin
 * @author     Studio Espresso <jan@studioespresso.co>
 */
class Wp_Splashing_Unsplash {

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

	}

	public function setup() {
	    return Crew\Unsplash\HttpClient::init(array(
	        'applicationId' => '7f3b78cd15141810237aaa5e7242e8fc4df9ba72a99fbd51612554ea72cc60e4'
        ));
	}

	public function getLastFeatured($count = 10) {
	    if(get_transient('splashing_featured')) {
	        return unserialize(get_transient('splashing_featured'));
        } else {
            $this->setup();
            $images = Crew\Unsplash\Photo::curated(1, $count);
            set_transient('splashing_featured', $images, 12 * HOUR_IN_SECONDS);
            return $images;
        }
    }

    public function getCategories() {
    	$this->setup();
    	$categories = Crew\Unsplash\Category::all(1, 200);
    	return $categories;
    }
    // $search, $category = null, $page = 1, $per_page = 10, $orientation = null
    public function search($string, $page = 1) {
    	$this->setup();
    	$search = Crew\Unsplash\Search::photos($string, $page);
        if(count($search->results) < 1) {
            return $data['results'] = false;
        } else {
            $data['pagination']['total_pages'] = $search->total_pages;
            $data['pagination']['total_results'] = $search->total;
            $data['pagination']['base'] = '/wp-admin/upload.php?page=wp-splashing';
            foreach ($search->results as $image) {
                $image->urls = (array)$image->urls;
                $image->links = (array)$image->links;
                $image->user = (array)$image->user;
            }
            $data['results'] = $search->results;
            return $data;
        }

    }

}
