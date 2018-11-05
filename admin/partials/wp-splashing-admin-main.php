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
    <h1>
		<?php _e('Splashing Images', 'wp-splashing-images')?>
		<span style="filter: grayscale(100%);">&#128247; </span>
	</h1>
		<?php if(isset($_GET['session'])) {
			$data = base64_decode($_GET['session']);
			$this->unsplash->saveTokens($data);
			$user = $this->unsplash->getUser(); ?>
			<div class="notice inline notice-info notice-alt">
				<p>
					<?php
					echo sprintf( __('You\'re now connected to Unsplash as <a href="%1$s" target="_blank">%2$s</a>', 'wp-splashing-image'), $user->links['html'], $user->username);
					?>
				</p>
			</div>
<?php } ?>

	<h2 class="nav-tab-wrapper">

		<a href="/wp-admin/upload.php?page=wp-splashing&mode=popular" class="nav-tab <?php echo $_GET['mode'] == 'popular' || ( $_GET['mode'] == '' && !$_GET['search'] ) ? 'nav-tab-active' : ''; ?>" alt="<?php _e('25 most popular photos on Unsplash', 'wp-splashing-images'); ?>"><span style="color: green;">&#8599; </span><?php _e('Popular', 'wp-splashing-images'); ?></a>
		<a href="/wp-admin/upload.php?page=wp-splashing&mode=latest" class="nav-tab <?php echo $_GET['mode'] == 'latest' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Shows the 25 last added images', 'wp-splashing-images'); ?>"><?php _e('&#128349; Last added', 'wp-splashing-images'); ?></a>
		<?php if($this->unsplash->isUnsplashUser()) { ?>
		<a href="/wp-admin/upload.php?page=wp-splashing&mode=liked" class="nav-tab <?php echo $_GET['mode'] == 'liked' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Shows the images you like on unsplash.com', 'wp-splashing-images'); ?>"><span style="color: red;">&hearts; </span><?php _e('Photos you liked', 'wp-splashing-images'); ?></a>
		<a href="/wp-admin/upload.php?page=wp-splashing&mode=mine" class="nav-tab <?php echo $_GET['mode'] == 'mine' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Show the photos you added to unsplasho.com', 'wp-splashing-images'); ?>"><span style="color: gold;">&#128100; </span><?php _e('Your photos', 'wp-splashing-images'); ?></a>
			<?php if($this->unsplash->userHasCollections()) { ?>
				<a href="/wp-admin/upload.php?page=wp-splashing&mode=collections" class="nav-tab <?php echo $_GET['mode'] == 'collections' || $_GET['mode'] == 'collection' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Shows an overview of the collections you created on unsplash.com', 'wp-splashing-images'); ?>"><span style="color: orangered;">&#9889; </span><?php _e('Your Collections', 'wp-splashing-images'); ?></a>
			<?php } ?>
		<?php } ?>

	</h2>
	<div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
			<div id="wp-splashing_images" style="position: relative;" class="postbox-container">
				<div id="splashing-images">
					<?php
					if(isset($_GET['search'])) {
						$data = $this->unsplash->search($_GET['search'], $_GET['paged']);
						$images = $data['results'];
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'latest') {
                        $images = $this->unsplash->getLatest(25);
                    } elseif(isset($_GET['mode']) && $_GET['mode'] == 'popular') {
                        $images = $this->unsplash->getPopular(25);
                    } elseif(isset($_GET['mode']) && $_GET['mode'] == 'liked') {
						$images = $this->unsplash->getLiked(1, 100);
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'mine') {
						$images = $this->unsplash->getOwnImages(1, 100);
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'collections') {
						$collections = $this->unsplash->getCollections();
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'collection' && isset($_GET['id'])) {
						$images = $this->unsplash->getCollection($_GET['id']);
					} else {
						$images = $this->unsplash->getLastFeatured(24);
					}
					if(isset($images) && $images != false) {
						echo '<div id="splashing-container">';
						foreach($images as $image) {
							$thumb = $image->urls['thumb'];
							$download = $image->links['download'];
							$author = $image->user['name'];
							echo '
							<div class="splashing" data-id="' . $image->id . '"> 
								<img src="' . $thumb .'">
								<span class="attribute"><a href="http://unsplash.com">' . $author .'</a></span>
							</div>';
						}
						echo '</div>';
					} elseif ($collections) {
						echo '<div id="splashing-container">';
						foreach($collections as $collection ){
							echo '<a href="/wp-admin/upload.php?page=wp-splashing&mode=collection&id=' . $collection['id'] . '"><img src="' . $collection['urls']['thumb'] . '"></a>';
						}
					} else  {
						echo '<div><p>' . sprintf( __('Looks like me couldn\'t find any images for <strong><em>%1$s</em></strong>, try searching for something else or surprise yourself.','wp-splashing-images'), $_GET['search']) . '</p></div>';
					} ?>
				</div>
				<?php
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
						'prev_text'          => __('« Previous', 'wp-splashing-images'),
						'next_text'          => __('Next »', 'wp-splashing-images'),
						'type'               => 'plain',
						'add_args'           => false,
					);
					echo '<div class="splashing-pagination">';
					echo paginate_links( $args );
					echo '</div>';
				}
				?>
			</div>
			<?php require(plugin_dir_path( __FILE__ ) . 'wp-splashing-admin-sidebar.php'); ?>
    	</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
        var grid = jQuery('#splashing-container').masonry({
            itemSelector: 'div.splashing',
            gutter: 10,
        });
        grid.imagesLoaded().progress(function () {
            grid.masonry();
        });
	});
</script>
