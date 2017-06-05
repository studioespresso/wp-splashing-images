<?php

/**
 * The unsplash-specific functionality of the plugin.
 *
 * @link       http://studioespresso.co
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


    const REDIRECT_URL = "https://studioespresso.co/wp-splashing";

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

	public function setupWithUser() {
        return Crew\Unsplash\HttpClient::init(
            array(
            'applicationId' => '7f3b78cd15141810237aaa5e7242e8fc4df9ba72a99fbd51612554ea72cc60e4',
            ),
            array(
                'access_token' => $this->getAccessToken(),
                'expires_in' => 300000,
            )
        );
    }

    public function getAuthUrl() {
        $url = self::REDIRECT_URL . '?redirect=' . admin_url() . 'upload.php?page=wp-splashing';
        return $url;
    }

    public function saveTokens($session) {
        update_option('splashing_access_token', $session->getToken());
    }

    public function isUnsplashUser() {
        if(get_option('splashing_access_token', null)) {
            return true;
        }
        return false;
    }

    public function userHasCollections() {
        $user = $this->getUser();
        $collections = $user->collections();
        return count($collections) > 0 ? true : false;
    }

    public function getAccessToken() {
        return get_option('splashing_access_token', null);
    }

    public function getUser() {
        $this->setupWithUser();
        $user = Crew\Unsplash\User::current();
        return $user;
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

    public function getRandom($count = 25) {
        $this->setup();
        $data = Crew\Unsplash\Photo::random(
            array(
                'count' => $count
            )
        );
        return $data;
    }

    public function getLatest($count = 25) {
        $this->setup();
        $images = Crew\Unsplash\Photo::all($page = 1, $per_page = $count, $orderby = 'latest');
        return $images;
    }

    public function getPopular($count = 25) {
        if(get_transient('splashing_latest')) {
            return unserialize(get_transient('splashing_latest'));
        } else {
            $this->setup();
            $images = Crew\Unsplash\Photo::all($page = 1, $per_page = $count, $orderby = 'popular');
            set_transient('splashing_latest', $images, 6 * HOUR_IN_SECONDS);
            return $images;

        }
    }

    public function getLiked($page = 1, $count = 25) {
        $user = $this->getUser();
        $result = $user->likes($page, $count);
        return $result;
    }

    public function getOwnImages($page = 1, $count = 25) {
        $user = $this->getUser();
        $result = $user->photos($page, $count);
        return $result;
    }

    public function getCollections() {
        $user = $this->getUser();
        $collections = $user->collections();
        $results = array();

        foreach($collections as $collection) {
            $results[$collection->id]['id'] = $collection->id;
            $results[$collection->id]['urls'] = $collection->cover_photo['urls'];
        }
        return $results;
    }

    public function getCollection($id) {
        $this->setupWithUser();
        $collection = Crew\Unsplash\Collection::find($id);
        //var_dump($collection->photos()); exit;
        return $collection->photos();
    }

    // $search, $category = null, $page = 1, $per_page = 10, $orientation = null
    public function search($string, $page = 1) {
        $transient = 'splashing_search_' . $string . '_' . $page;
        if(get_transient($transient)) {
            return get_transient($transient);
        } else {
            $this->setup();
            $search = Crew\Unsplash\Search::photos($string, $page);
            $transient = 'splashing_search_' . $string . '_' . $page;
            if(count($search->results) < 1) {
                $data['results'] = false;
                set_transient($transient, $data, 48 * HOUR_IN_SECONDS);
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
                set_transient($transient, $data, 48 * HOUR_IN_SECONDS);

            }

            return $data;
        }

    }

}
