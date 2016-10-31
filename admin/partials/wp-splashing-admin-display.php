<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://studioepresso.co
 * @since      1.0.0
 *
 * @package    Wp_Splashing
 * @subpackage Wp_Splashing/admin/partials
 */
?>

<div class="wrap">
    <h1><?php _e('Splashing Images', 'wp-splashing')?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
                <div id="splashing_images" style="position: relative;" class="postbox-container">
	                <div class="media-toolbar wp-filter"><div class="media-toolbar-secondary"><div class="view-switch media-grid-view-switch">
	                        <a href="/wp-admin/upload.php?page=wp-splashing&mode=list" class="view-list">
	                            <span class="screen-reader-text">List View</span>
	                        </a>
	                        <a href="/wp-admin/upload.php?page=wp-splashing&mode=grid" class="view-grid current">
	                            <span class="screen-reader-text">Grid View</span>
	                        </a>
	                    </div>
	                    <form id="splashing-search" method="get" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
	                        <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
	                        <input type="search" id="post-search-input" name="search" value="<?php echo $_GET['search']; ?>">
	                        <input type="hidden" name="action" value="wp_splashing_search">
							<input type="hidden" name="paged" value="1">
	                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wp_splashing_nonce'); ?>">
	                        <input type="submit" id="search-submit" class="button" value="Search Unsplash">
	                    </form>
	                    </div>
	                </div>
	                <div id="splashing-images">
	                	<?php
	                	if(isset($_GET['search'])) {
	                		$data = $this->unsplash->search($_GET['search'], $_GET['paged']);
							$images = $data['results'];
	                	} else {
		                    $images = $this->unsplash->getLastFeatured(24);
	                	}
	         			if($images != false) {
							echo '<div class="wrapper">';
	                    	foreach($images as $image) {
	                    	    $thumb = $image->urls['thumb'];
                                $download = $image->links['download'];
                                $author = $image->user['name'];
                                echo '<a href="" class="upload" data-source="' . $download . '" data-author="' . $author . '"><img class="splashing-thumbnail" src="' . $thumb .'"></a>';
	                    	}
	                    	echo '</div>';
						$args = array(
							'base' 				 => preg_replace('/\?.*/', '', get_pagenum_link()) . '%_%',
 							'format'             => '?paged=%#%',
							'total'              => $data['pagination']['total_pages'],
							'current'            => $_GET['paged'],
							'show_all'           => false,
							'end_size'           => 2,
							'mid_size'           => 3,
							'prev_next'          => true,
							'prev_text'          => __('« Previous'),
							'next_text'          => __('Next »'),
							'type'               => 'plain',
							'add_args'           => false,
							'add_fragment'       => '',
							'before_page_number' => '',
							'after_page_number'  => ''
						);
						echo '<div class="splashing-pagination">';
						echo paginate_links( $args );
						echo '</div>';
	                    } else {
							echo "NO RESULTS";
						} ?>

					</div>
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
