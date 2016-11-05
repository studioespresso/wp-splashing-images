<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://studioespresso.co
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
				<div class="media-toolbar wp-filter">
					<div class="media-toolbar-primary">
					</div>
                    <div class="media-toolbar-primary">
                        <a href="/wp-admin/upload.php?page=wp-splashing&mode=popular" class="button btn--inline" alt="<?php _e('25 most popular photos on Unsplash', 'wp-splashing'); ?>"><span style="color: red;">&hearts; </span><?php _e('Popular', 'wp-splashing'); ?></a>
                        <a href="/wp-admin/upload.php?page=wp-splashing&mode=latest" class="button btn--inline" alt="<?php _e('Shows the 25 last added images', 'wp-splashing'); ?>"><?php _e('&#128349; Last added', 'wp-splashing'); ?></a>
                        <a href="/wp-admin/upload.php?page=wp-splashing&mode=random" class="button btn--inline" alt="<?php _e('Shows 25 random images', 'wp-splashing'); ?>"><span style="color: gold;">&#9733; </span><?php _e('Surprise me :)', 'wp-splashing'); ?></a>
                    </div>
                    <div class="media-toolbar-secondary">

                        <form id="splashing-search" method="get" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
						<label class="screen-reader-text" for="post-search-input">Search Posts:</label>
						<input type="search" id="post-search-input" name="search" value="<?php echo $_GET['search']; ?>">
						<input type="hidden" name="action" value="wp_splashing_search">
						<input type="hidden" name="paged" value="1">
						<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wp_splashing_nonce'); ?>">
						<input type="submit" id="search-submit" class="button" value="<?php _e('&#128269; Search Unsplash','wp-splashing');?>">
					</form>
					</div>
				</div>
				<div id="splashing-images">
					<?php
					if(isset($_GET['search'])) {
						$data = $this->unsplash->search($_GET['search'], $_GET['paged']);
						$images = $data['results'];
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'random') {
                        $images = $this->unsplash->getRandom(25);
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'latest') {
                        $images = $this->unsplash->getLatest(25);
                    } elseif(isset($_GET['mode']) && $_GET['mode'] == 'popular') {
                        $images = $this->unsplash->getLatest(25);
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
                        if(isset($data['pagination'])) {
                            $args = array(
                                'base' 				 => preg_replace('/\?.*/', '', get_pagenum_link()) . '%_%',
                                'format'             => '?paged=%#%',
                                'total'              => $data['pagination']['total_pages'],
                                'current'            => $_GET['paged'],
                                'show_all'           => false,
                                'end_size'           => 2,
                                'mid_size'           => 3,
                                'prev_next'          => true,
                                'prev_text'          => __('« Previous', 'wp-splashing'),
                                'next_text'          => __('Next »', 'wp-splashing'),
                                'type'               => 'plain',
                                'add_args'           => false,
                            );
                            echo '<div class="splashing-pagination">';
                            echo paginate_links( $args );
                            echo '</div>';
                        }
					} else {
						echo "NO RESULTS";
					} ?>

				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<h2 class="hndle splashing"><?php _e('Powered by Unsplash', 'wp-splashing'); ?></h2>
					<div class="inside">
						<p><?php _e('Splashing Images is powered by <a href="http://unsplash.com">unsplash.com</a> and the Unsplash API.', 'wp-splashing'); ?></p>
						<h3><?php _e('Unsplash License', 'wp-splashing'); ?></h3>
						<p><?php _e('All photos published on Unsplash are licensed under <a href="https://creativecommons.org/publicdomain/zero/1.0/">Creative Commons Zero</a> which means you can copy, modify, distribute and use the photos for free, including commercial purposes, without asking permission from or providing attribution to the photographer or Unsplash.', 'wp-splashing'); ?></p>
					</div>
				</div>
			</div>
    	</div>
	</div>
</div>
