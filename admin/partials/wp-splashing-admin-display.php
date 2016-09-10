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
	                        <input type="search" id="post-search-input" name="search" value="">
	                        <select id="orientation" name="orientation">
	                        	<option value=""><?php _e('Any orientation', 'wp-splashing'); ?></option>
	                        	<option value="landscape"><?php _e('Landscape', 'wp-splashing'); ?></option>
	                        	<option value="portrait"><?php _e('Portrait', 'wp-splashing'); ?></option>
	                        </select>
	                        <input type="hidden" name="action" value="wp_splashing_search">
	                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wp_splashing_nonce'); ?>">
	                        <input type="submit" id="search-submit" class="button" value="Search Unsplash">
	                    </form> 
	                    </div>
	                </div>
	                <div id="splashing-images">
	                	<?php 
	                	if(isset($_GET['search'])) {
	                		$images = $this->unsplash->search($_GET['search'], $_GET['orientation']);
	                	} else {
		                    $images = $this->unsplash->getLastFeatured(50);
	                	}  

	         			if(isset($images)) {
	                    	foreach($images as $image) {
	                        	 echo '<a href="" class="upload" data-source="' . $image->links['download'] . '"><img class="splashing-thumbnail" src="' . $image->urls['thumb'] .'"></a>';
	                    	} 
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
