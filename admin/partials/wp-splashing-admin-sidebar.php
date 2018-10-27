<div id="postbox-container-1" class="postbox-container">
    <div class="postbox">

        <div class="inside">
            <div class="media-toolbar-search-splashing">

                <form id="splashing-search" method="get" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
                    <input type="search" id="post-search-input-splashing" name="search" value="<?php echo esc_attr(isset($_GET['search'])); ?>" placeholder="<?php _e('Search unsplash.com', 'wp-splashing-images'); ?>">
                    <input type="hidden" name="action" value="wp_splashing_search">
                    <input type="hidden" name="paged" value="1">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wp_splashing_nonce'); ?>">
                    <input type="submit" id="search-submit-splashing" class="button" value="<?php _e('&#128269;', 'wp-splashing-images'); ?>">
                </form>
            </div>
        </div>
    </div>
    <div class="postbox">
        <h2 class="hndle oauth"><?php _e('Personalize your images', 'wp-splashing-images'); ?></h2>
        <div class="inside">
            <?php echo sprintf(__('You can connect your Unsplash account to get access your own images and the images you liked on Unsplash here.', 'wp-splashing-images')); ?>

            <?php if(!$this->unsplash->getAccessToken()) {
                echo sprintf(__('<p>Don\'t have an account yet? Get one <a href="%1$s">here</a></p>', 'wp-splashing-images'), 'https://unsplash.com/join');
            } ?>
            <?php if ($this->unsplash->getAccessToken()) {
                $user = $this->unsplash->getUser();
                ?>
                <p><strong>
                    <?php
                    echo sprintf(__('Logged in as <a href="%1$s" target="_blank">%2$s</a>.', 'wp-splashing-images'), $user->links['html'], $user->username); ?>
                    </strong>
                    <?php echo sprintf(__('<a href="%1$s">Disconnect?</a>', 'wp-splashing-images'), admin_url() . 'upload.php?page=wp-splashing&disconnect=true'); ?>
                </p>
            <?php } else { ?>
                <p>
                    <a href="<?php echo $this->unsplash->getAuthUrl(); ?>" target="_blank" class="button"><?php _e('Log in with Unsplash', 'wp-splashing-images'); ?></a>
                </p>

            <?php }; ?>
        </div>
    </div>
    <div class="postbox">
        <h2 class="hndle splashing"><?php _e('Powered by Unsplash', 'wp-splashing-images'); ?></h2>
        <div class="inside">
            <p><?php _e('Splashing Images is powered by <a href="http://unsplash.com">unsplash.com</a> and the Unsplash API.', 'wp-splashing-images'); ?></p>
            <h3><?php _e('Unsplash License', 'wp-splashing-images'); ?></h3>
            <p><?php _e('All photos published on Unsplash are licensed under <a href="https://creativecommons.org/publicdomain/zero/1.0/">Creative Commons Zero</a> which means you can copy, modify, distribute and use the photos for free, including commercial purposes, without asking permission from or providing attribution to the photographer or Unsplash.', 'wp-splashing-images'); ?></p>
        </div>
        <hr>
        <div class="inside">
            <h3><?php _e('From <a href="http://studioespresso.co/en?utm_source=plugin&amp;utm_medium=plugin_detail&amp;utm_campaign=wp-splashing-images" target="_blank">Studio Espresso</a>, with', 'wp-splashing-images'); ?>
                <span style="color: red;">&hearts;</span></h3>
            <p><?php _e("We'd love to hear what you think about the plugin so feel free to get in touch with your <a href='mailto:support@studioespresso.co'>suggestions</a> or <a href='https://wordpress.org/support/plugin/wp-splashing-images' target='_blank'>questions</a>", "wp-splashing-images"); ?></p>
        </div>
    </div>
</div>
