<div id="postbox-container-1" class="postbox-container">
    <div class="postbox">
        <h2 class="hndle oauth"><?php _e('Personalize your images', 'wp-splashing-images'); ?></h2>
        <div class="inside">
            If you have an Unsplash account, you can connect it here to get access to your collections.
            <?php if ($this->unsplash->getAccessToken()) {
                $user = $this->unsplash->getUser();
                ?>
                <p>
                    <?php
                    echo sprintf(__('Logged in as <a href="%1$s" target="_blank">%2$s</a>. <a href="%3$s">Disconnect?</a>', 'wp-splashing-image'), $user->links['html'], $user->username, admin_url() . 'upload.php?page=wp-splashing&disconnect=true');
                    ?>
                </p>
            <?php } else { ?>
                <p>
                    <a href="<?php echo $this->unsplash->getAuthUrl(); ?>" target="_blank" class="button">Connect</a>
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
            <h3><?php _e('By <a href="http://studioespresso.co/en?utm_source=plugin&amp;utm_medium=plugin_detail&amp;utm_campaign=wp-splashing-images" target="_blank">Studio Espresso</a>, with', 'wp-splashing-images'); ?>
                <span style="color: red;">&hearts;</span></h3>
            <p><?php _e("We'd love to hear what you think about the plugin so feel free to get in touch with your <a href='mailto:support@studioespresso.co'>suggestions</a> or <a href='https://wordpress.org/support/plugin/wp-splashing-images' target='_blank'>questions</a>", "wp-splashing-images"); ?></p>
        </div>
    </div>
</div>
