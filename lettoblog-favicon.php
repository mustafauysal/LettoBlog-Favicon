<?php
/*
Plugin Name: LettoBlog Favicon
Plugin URI: http://uysalmustafa.com/plugins/lettoblog-favicon/
Description: The easiest way to put a favicon on your site. You can upload favicon or use your gravatar image as favicon. If you are using multisite, you can set network-wide favicon.
Version: 1.3
Author: Mustafa UYSAL
Author URI: http://uysalmustafa.com
Text Domain: favicon
Domain Path: /lang/
License:GPLv2 or later
Network: True
*/



/**
 * localize plugin
 * @since 1.0
 */
function localize_plugin()
{
    load_plugin_textdomain( 'favicon', false,  dirname( plugin_basename( __FILE__ ) ) . '/lang/');
}


/**
 * Options menu
 * @since 1.0
 */
function favicon_option_menu()
{
    $network_favicon = get_site_option('lettoblog_network_favicon');
    if (is_multisite()) {
        add_submenu_page('settings.php', __('Favicon', 'favicon'), __('Favicon', 'favicon'), 'manage_network_options', 'lettoblog-favicon', 'favicon_options_network');
        if ($network_favicon['network_wide_favicon'] == false) {
            add_options_page(__('Favicon', 'favicon'), __('Favicon', 'favicon'), 'manage_options', 'lettoblog-favicon', 'favicon_options_page');
        }
    } else {
        add_options_page(__('Favicon', 'favicon'), __('Favicon', 'favicon'), 'manage_options', 'lettoblog-favicon', 'favicon_options_page');
    }
}


/**
 * Load required js files for media loader.
 * @since 1.2
 */
function load_favicon_js() {
    if ( isset( $_GET['page'] ) && $_GET['page'] == 'lettoblog-favicon' ) {
        wp_enqueue_media();
        wp_register_script( 'favicon_js', plugins_url( '/lettoblog-favicon/favicon.js' ), array( 'jquery' ) );
        wp_enqueue_script( 'favicon_js' );
    }
}


/**
 * Check file extention is really favicon?
 * @param $file
 * @return bool
 * @since 1.2
 */
function _is_it_ico($file)
{
    $ext = substr($file, -4);
    if ($ext === '.ico') {
        return true;
    }
    return false;
}

/**
 * Use gravatar as favicon
 * @since 1.2
 */
function use_gravatar_as_favicon()
{
    $options = get_option('favicon_insert_url');

    if ( is_null( $options['gravatar_email'] ) || ! is_email( $options['gravatar_email'] ) ) {
        $email = get_bloginfo( 'admin_email' );
    } else {
        $email = $options['gravatar_email'];
    }
    $hash = md5(strtolower($email));
    return 'http://www.gravatar.com/avatar/' . $hash . '?s=16';
}


/**
 * Save site's favicon settings
 * @since 1.3
 */
function save_favicon_options(){

    if ( isset( $_POST['update_options'] ) ) {
        $options['favicon_url'] = sanitize_text_field( $_POST['favicon_url'] );
        $options['gravatar_email'] = sanitize_text_field($_POST['gravatar_email']);
        $options['gravatar_favicon']  = false;
        // maybe gravatar is favicon
        if ( isset( $_POST['gravatar_favicon'] ) && true == $_POST['gravatar_favicon'] ) {
            $options['gravatar_favicon'] = true;
        }

        if ( true !== $options['gravatar_favicon'] && ! _is_it_ico( $options['favicon_url'] ) ) {
            $options['favicon_url'] = null;
            echo '<div class="error"><p>' . __( 'Favicon extention should be .ico!', 'favicon' ) . '</p></div>';
        }

        update_option( 'favicon_insert_url', $options );

        echo '<div class="updated"><p>' . __( 'Options saved', 'favicon' ) . '</p></div>';
    }


}


/**
 * Favicon options page
 * @since 1.0
 */
function favicon_options_page()
{
    $options = get_option( 'favicon_insert_url' );

    ?>



    <div class="wrap">
        <h2><?php echo __('Favicon Settings', 'favicon'); ?></h2>

        <form method="post" action="">

            <table>
                <tr>
                <label for="favicon_url">
                    <?php _e( 'Favicon URL:', 'favicon' ) ?> <input id="favicon_url" type="text" size="50" value="<?php echo esc_url( $options['favicon_url'] ); ?>" name="favicon_url" />
                    <input class="button" type="button" name="favicon_upload" id="favicon_upload" class="favicon_upload" value="<?php _e('Favicon Upload', 'favicon'); ?>"/>
                    <br/> <?php echo __('Enter your favicon url (direct link).Example:<em>http://lettoblog.com/favicon.ico</em><br />Do not forget, refresh your browser to see changes.', 'favicon'); ?>
                    <br/><br/>
                </label>
                </tr>
                <tr>
                    <label>
                        <input type="checkbox" <?php if($options['gravatar_favicon']==true){echo "checked";}?> name="gravatar_favicon" value="1"> <?php _e('Use gravatar image as favicon','favicon');?><br/>
                        <?php _e( 'Gravatar e-mail:', 'favicon' ); ?> <input type="text" size="50" name="gravatar_email" value="<?php echo esc_attr( $options['gravatar_email'] ); ?>" id="gravatar_email" /><br />
                        <?php _e('If leave empty, admin email address would be used.','favicon');?><br/><br/>
                    </label>
                </tr>

                <input name="update_options" class="button" type="submit" value="<?php _e('Update', 'favicon') ?>"/>
            </table>

        </form>

    </div>
<?php
}

function favicon_options_network()
{
    if ( isset( $_POST['update_network_options'] ) ) {
        $favicon_url = sanitize_text_field( $_POST['network_favicon_url'] );

        if ( ! empty( $_POST['favicon_url'] ) && ! _is_it_ico( $favicon_url ) ) {
            echo '<div class="error"><p>' . __( 'Favicon extention should be .ico!', 'favicon' ) . '</p></div>';
        } else {
            if ( isset( $_POST['network_wide_favicon'] ) && ( $_POST['network_wide_favicon'] === '1' ) ) {
                $options['network_wide_favicon'] = true;
            } else {
                $options['network_wide_favicon'] = false;
            }

            $options['network_favicon_url'] = $favicon_url;
            update_site_option( 'lettoblog_network_favicon', $options );

            echo '<div class="updated"><p>' . __( 'Options saved', 'favicon' ) . '</p></div>';
        }
    }
    $network_favicon = get_site_option('lettoblog_network_favicon');


    ?>
    <div class="wrap">
        <h2><?php echo __('Network Favicon Settings', 'favicon'); ?></h2>

        <form method="post" action="">
            <table>
                <tr>
                    <label>
                        <input type="checkbox" <?php  if(($network_favicon['network_wide_favicon']) === true){echo "checked";}?> name="network_wide_favicon" value="1"> <?php _e('Use this favicon for all sites','favicon');?><br/>
                    </label>
                </tr>
                <tr>
                    <label for="network_favicon_url">
                        <?php _e('Network Favicon URL:', 'favicon') ?>   <input id="network_favicon_url" type="text" size="50" value="<?php echo esc_url($network_favicon['network_favicon_url']); ?>" name="network_favicon_url" />
                        <input class="button" type="button" name="network_favicon_upload" id="network_favicon_upload" class="network_favicon_upload" value="<?php _e('Favicon Upload', 'favicon'); ?>"/>
                        <br/> <?php echo __('Enter your favicon url (direct link).Example:<em>http://lettoblog.com/favicon.ico</em><br />Do not forget, refresh your browser to see changes.', 'favicon'); ?>
                        <br/><br/>
                    </label>
                </tr>
                <input name="update_network_options" class="button" type="submit" value="<?php _e('Update', 'favicon') ?>"/>
            </table>

        </form>

    </div>


    <?php
}

/**
 * Okay. Let's adding favicon
 */
function favicon_display()
{
    $network_options = get_site_option('lettoblog_network_favicon');
    $options = get_option('favicon_insert_url');

    if (is_multisite() && $network_options['network_wide_favicon'] === true) {
        echo '<link rel="shortcut icon" href="' . esc_url( $network_options['network_favicon_url'] ) . '" type="image/x-icon" />';
        echo '<!-- LettoBlog Favicon -->';
    } else {
        if ($options['gravatar_favicon']  && $options['gravatar_favicon'] == true) {
            echo '<link rel="shortcut icon" href="' . use_gravatar_as_favicon() . '" />';
            echo '<!-- LettoBlog Favicon -->';
        } else if (!empty($options['favicon_url'])) {
            echo '<link rel="shortcut icon" href="' . esc_url($options['favicon_url']) . '" type="image/x-icon" />';
            echo '<!-- LettoBlog Favicon -->';
        }
    }

}

/**
 * WordPress 4.3 site icon warning
 */
function site_icon_warning() {
    global $wp_version;

    if ( is_admin() && current_user_can( 'manage_options' ) && version_compare( $wp_version, '4.3', '>=' ) ) {
        echo '<div class="update-nag" id="message"><p>LettoBlog favicon has been retired. Please use WordPress built in "Site Icon" feature. The Site Icon feature can be found by going to Appearance -> Customize and clicking on Site Identity.</p></div>';
    }
}


//hooks
add_action('admin_head', 'favicon_display');
add_action('wp_head', 'favicon_display');
add_action('admin_enqueue_scripts', 'load_favicon_js');
add_action('plugins_loaded', 'localize_plugin');
add_action('network_admin_menu', 'favicon_option_menu');
add_action('admin_menu', 'favicon_option_menu');
add_action( 'admin_init', 'save_favicon_options' );
if ( is_multisite() ) {
    add_action( 'network_admin_notices', 'site_icon_warning' );
} else {
    add_action( 'admin_notices', 'site_icon_warning' );
}