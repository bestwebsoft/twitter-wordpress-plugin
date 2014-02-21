<?php
/*
Plugin Name: Twitter
Plugin URI:  http://bestwebsoft.com/plugin/
Description: Plugin to add a link to the page author to twitter.
Author: BestWebSoft
Version: 2.33
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*
	@ Copyright 2014  BestWebSoft  ( http://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );

/* Add BWS menu */
if ( ! function_exists ( 'twttr_add_pages' ) ) {
	function twttr_add_pages() {
		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( 'images/px.png', __FILE__ ), 1001 );
		add_submenu_page( 'bws_plugins', __( 'Twitter Settings', 'twitter' ), __( 'Twitter', 'twitter' ), 'manage_options', 'twitter.php', 'twttr_settings_page' );
	}
}

/* Register settings for plugin */
if ( ! function_exists( 'twttr_settings' ) ) {
	function twttr_settings() {
		global $wpmu, $twttr_options, $bws_plugin_info;

		if ( function_exists( 'get_plugin_data' ) && ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) ) ) {
			$plugin_info = get_plugin_data( __FILE__ );	
			$bws_plugin_info = array( 'id' => '76', 'version' => $plugin_info["Version"] );
		}

		$twttr_options_default = array(
			'url_twitter' 		=>	'admin',
			'display_option'	=>	'custom',
			'count_icon' 		=>	1,
			'img_link' 			=>	plugins_url( "images/twitter-follow.gif", __FILE__ ),
			'position' 			=>	'',
			'disable' 			=>	'0'
		);
		/* Install the option defaults */
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'twttr_options' ) ) {
				if ( false !== get_site_option( 'twttr_options_array' ) ) {
					$old_options = get_site_option( 'twttr_options_array' );
					foreach ( $twttr_options_default as $key => $value ) {
						if ( isset( $old_options['twttr_' . $key] ) )
							$twttr_options_default[$key] = $old_options['twttr_' . $key];
					}
					delete_site_option( 'twttr_options_array' );
				}
				add_site_option( 'twttr_options', $twttr_options_default, '', 'yes' );
			}
		} else {
			if ( ! get_option( 'twttr_options' ) ) {
				if ( false !== get_option( 'twttr_options_array' ) ) {
					$old_options = get_option( 'twttr_options_array' );
					foreach ( $twttr_options_default as $key => $value ) {
						if ( isset( $old_options['twttr_' . $key] ) )
							$twttr_options_default[$key] = $old_options['twttr_' . $key];
					}
					delete_option( 'twttr_options_array' );
				}
				add_option( 'twttr_options', $twttr_options_default, '', 'yes' );
			}
		}
		/* Get options from the database */
		if ( 1 == $wpmu )
			$twttr_options = get_site_option( 'twttr_options' ); /* Get options from the database */
		else
			$twttr_options = get_option( 'twttr_options' ); /* Get options from the database */
		$twttr_options = array_merge( $twttr_options_default, $twttr_options );
		update_option( 'twttr_options', $twttr_options );
	}
}

/* Add Setting page */
if ( ! function_exists( 'twttr_settings_page' ) ) {
	function twttr_settings_page() {
		global $twttr_options;
		$copy = false;

		if ( false !== @copy( plugin_dir_path( __FILE__ ) . "images/twitter-follow.jpg", plugin_dir_path( __FILE__ ) . "images/twitter-follow1.jpg" ) )
			$copy = true;

		$message	=	"";
		$error		=	"";
		if ( isset( $_REQUEST['twttr_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'twttr_nonce_name' ) ) {
			$twttr_options['url_twitter']		=	$_REQUEST['twttr_url_twitter'];
			$twttr_options['display_option' ]	=	$_REQUEST['twttr_display_option'];
			$twttr_options['position']			=	$_REQUEST['twttr_position'];
			$twttr_options['disable']			=	isset( $_REQUEST["twttr_disable"] ) ? 1 : 0;
			if ( isset( $_FILES['upload_file']['tmp_name'] ) &&  $_FILES['upload_file']['tmp_name'] != "" )
				$twttr_options['count_icon']	=	$twttr_options['count_icon'] + 1;
			if ( 2 < $twttr_options['count_icon'] )
				$twttr_options['count_icon']	=	1;

			update_option( 'twttr_options', $twttr_options );
			$message = __( "Settings saved", 'twitter' );

			/* Form options */
			if ( isset( $_FILES['upload_file']['tmp_name'] ) && "" != $_FILES['upload_file']['tmp_name'] ) {
				$max_image_width	=	100;
				$max_image_height	=	100;
				$max_image_size		=	32 * 1024;
				$valid_types 		=	array( 'jpg', 'jpeg' );
				/* Construction to rename downloading file */
				$new_name			=	'twitter-follow' . $twttr_options['count_icon'];
				$new_ext			=	'.jpg';
				$namefile			=	$new_name . $new_ext;
				$uploaddir			=	$_REQUEST['home'] . 'wp-content/plugins/twitter-plugin/images/'; /* The directory in which we will take the file: */
				$uploadfile			=	$uploaddir . $namefile;

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['upload_file'] ) && 'custom' == $_REQUEST['twttr_display_option'] )	{
					/* Checking is allowed download file given parameters */
					if ( is_uploaded_file( $_FILES['upload_file']['tmp_name'] ) ) {
						$filename	=	$_FILES['upload_file']['tmp_name'];
						$ext		=	substr( $_FILES['upload_file']['name'], 1 + strrpos( $_FILES['upload_file']['name'], '.' ) );
						if ( filesize ( $filename ) > $max_image_size ) {
							$error = __( "Error: File size > 32K", 'twitter' );
						} elseif ( ! in_array( $ext, $valid_types ) ) {
							$error = __( "Error: Invalid file type", 'twitter' );
						} else {
							$size = GetImageSize( $filename );
							if ( ( $size ) && ( $size[0] <= $max_image_width ) && ( $size[1] <= $max_image_height ) ) {
								/* If file satisfies requirements, we will move them from temp to your plugin folder and rename to 'twitter_ico.jpg' */
								if ( move_uploaded_file ( $_FILES['upload_file']['tmp_name'], $uploadfile ) ) {
									$message .= '. ' . __( "Upload successful.", 'twitter' );
								} else {
									$error = __( "Error: moving file failed", 'twitter' );
								}
							} else {
								$error = __( "Error: check image width or height", 'twitter' );
							}
						}
					} else {
						$error = __( "Uploading Error: check image properties", 'twitter' );
					}
				}
			}
		}
		twttr_update_option(); ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php echo __( "Twitter Settings", 'twitter' ); ?></h2>
			<div class="updated fade" <?php if ( empty( $message ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="twttr_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'twitter' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'twitter' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<div>
				<form method='post' action="admin.php?page=twitter.php" enctype="multipart/form-data" id="twttr_settings_form">
					<table class="form-table">
						<tr valign="top">
							<th scope="row" colspan="2"><?php echo __( 'Settings for the button "Follow Me":', 'twitter' ); ?></th>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php echo __( "Enter your username:", 'twitter' ); ?>
							</th>
							<td>
								<input name='twttr_url_twitter' type='text' value='<?php echo $twttr_options['url_twitter'] ?>'/><br />
								<span style="color: rgb(136, 136, 136); font-size: 10px;"><?php echo __( 'If you do not have Twitter account yet, you should create it using this link', 'twitter' ); ?> <a target="_blank" href="https://twitter.com/signup">https://twitter.com/signup</a> .</span><br />
								<span style="color: rgb(136, 136, 136); font-size: 10px;"><?php echo __( 'Paste the shortcode &lsqb;follow_me&rsqb; into the necessary page or post to use the "Follow Me" button.', 'twitter' ); ?></span><br />
								<span style="color: rgb(136, 136, 136); font-size: 10px;"><?php echo __( 'If you would like to use this button in some other place, please paste this line into the template source code', 'twitter' ); ?>	&#60;?php if ( function_exists( 'follow_me' ) ) echo follow_me(); ?&#62;</span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php echo __( "Choose display settings:", 'twitter' ); ?>
							</th>
							<td>
								<select name="twttr_display_option" onchange="if ( this . value == 'custom' ) { getElementById ( 'twttr_display_option_custom' ) . style.display = 'block'; } else { getElementById ( 'twttr_display_option_custom' ) . style.display = 'none'; }">
									<option <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="standart"><?php echo __( "Standard button", 'twitter' ); ?></option>
									<?php if ( $copy || 'custom' == $twttr_options['display_option'] ) { ?>
										<option <?php if ( 'custom' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="custom"><?php echo __( "Custom button", 'twitter' ); ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div id="twttr_display_option_custom" <?php if ( 'custom' == $twttr_options['display_option'] ) { echo ( 'style="display:block"' ); } else { echo ( 'style="display:none"' ); } ?>>
									<table>
										<th style="padding-left:0px;font-size:13px;">
											<?php echo __( "Current image:", 'twitter' ); ?>
										</th>
										<td>
											<img src="<?php echo $twttr_options['img_link']; ?>" />
										</td>
									</table>
									<table>
										<th style="padding-left:0px;font-size:13px;">
											<?php echo __( '"Follow Me" image:', 'twitter' ); ?>
										</th>
										<td>
											<input type="hidden" name="MAX_FILE_SIZE" value="64000"/>
											<input type="hidden" name="home" value="<?php echo ABSPATH ; ?>"/>
											<input type="file" name="upload_file" style="width:196px;" /><br />
											<span style="color: rgb(136, 136, 136); font-size: 10px;"><?php echo __( 'Image properties: max image width:100px; max image height:100px; max image size:32Kb; image types:"jpg", "jpeg".', 'twitter' ); ?></span>
										</td>
									</table>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" colspan="2"><?php echo __( 'Settings for the "Twitter" button:', 'twitter' ); ?></th>
						</tr>
						<tr>
							<th><?php echo __( 'Disable the "Twitter" button:', 'twitter' ); ?></th>
							<td>
								<input type="checkbox" name="twttr_disable" value="1" <?php if ( 1 == $twttr_options["disable"] ) echo "checked=\"checked\""; ?> />
								<span style="color: rgb(136, 136, 136); font-size: 10px;"> <?php echo __( 'The button "T" will not be displayed. Just the shortcode &lsqb;follow_me&rsqb; will work.', 'twitter' ); ?></span><br />
							</td>
						</tr>
						<tr>
							<th>
								<?php echo __( 'Choose the "Twitter" icon position:', 'twitter' ); ?>
							</th>
							<td>
								<label><input type="radio" name="twttr_position" value="1" <?php if ( 1 == $twttr_options['position'] ) echo 'checked="checked"'?> /> <?php echo __( 'Top position', 'twitter' ); ?></label><br />
								<label><input type="radio" name="twttr_position" value="0" <?php if ( 0 == $twttr_options['position'] ) echo 'checked="checked"'?> /> <?php echo __( 'Bottom position', 'twitter' ); ?></label><br />
								<span style="color: rgb(136, 136, 136); font-size: 10px;"><?php echo __( 'By clicking this icon a user can add the article he/she likes to his/her Twitter page.', 'twitter' ); ?></span><br />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="hidden" name="twttr_form_submit" value="submit" />
								<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'twitter' ) ?>" />
							</td>
						</tr>
					</table>
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'twttr_nonce_name' ); ?>
				</form>
				<br />
				<div class="bws-plugin-reviews">
					<div class="bws-plugin-reviews-rate">
					<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'twitter' ); ?>: 
					<a href="http://wordpress.org/support/view/plugin-reviews/twitter-plugin" target="_blank" title="Twitter reviews"><?php _e( 'Rate the plugin', 'twitter' ); ?></a><br/>
					</div>
					<div class="bws-plugin-reviews-support">
					<?php _e( 'If there is something wrong about it, please contact us', 'twitter' ); ?>: 
					<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
}

/* Function 'twttr_update_option' reacts to changes type of picture (Standard or Custom) and generates link to image, link transferred to array 'twttr_options' */
if ( ! function_exists( 'twttr_update_option' ) ) {
	function twttr_update_option () {
		global $twttr_options;
		if ( 'standart' == $twttr_options[ 'display_option' ] ) {
			$twttr_img_link	=	plugins_url( 'images/twitter-follow.jpg', __FILE__ );
		} else if ( 'custom' == $twttr_options['display_option'] ) {
			$twttr_img_link	= plugins_url( 'images/twitter-follow' . $twttr_options['count_icon'] . '.jpg', __FILE__ );
		}
		$twttr_options['img_link'] = $twttr_img_link;
		update_option( "twttr_options", $twttr_options );
	}
}

/* Function to creates shortcode [follow_me] */
if ( ! function_exists( 'twttr_follow_me' ) ) {
	function twttr_follow_me() {
		global $twttr_options;
		if ( 'standart' == $twttr_options[ 'display_option' ] ) {
			return '<div class="twttr_follow">
						<a href="https://twitter.com/' . $twttr_options["url_twitter"] . '" class="twitter-follow-button" data-show-count="true">Follow me</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>';
		} else {
			return '<div class="twttr_follow"><a href="http://twitter.com/' . $twttr_options["url_twitter"] . '" target="_blank" title="Follow me">
						<img src="' . $twttr_options['img_link'] . '" alt="Follow me" />
					</a></div>';
		}
	}
}

/* Positioning in the page	*/
if ( ! function_exists( 'twttr_twit' ) ) {
	function twttr_twit( $content ) {
		global $post, $twttr_options;
		$permalink_post	=	get_permalink($post->ID);
		$title_post		=	htmlspecialchars($post->post_title);
		if ( $title_post == 'your-post-page-title' )
			return $content;
		if ( 0 == $twttr_options['disable'] ) {
			$position = $twttr_options['position'];
			$str = '<div class="twttr_button">
						<a href="http://twitter.com/share?url=' . $permalink_post . '&text=' . $title_post . '" target="_blank" title="' . __( 'Click here if you like this article.', 'twitter' ) . '">
							<img src="' . plugins_url( 'images/twitt.gif', __FILE__ ) . '" alt="Twitt" />
						</a>
					</div>';
			if ( $position ) {
				return $str . $content;
			} else {
				return $content . $str;
			}
		} else {
			return $content;
		}
	}
}

if ( ! function_exists( 'twttr_action_links' ) ) {
	function twttr_action_links( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row */
		static $this_plugin;
		if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );
		if ( $file == $this_plugin ) {
			 $settings_link = '<a href="admin.php?page=twitter.php">' . __( 'Settings', 'twitter' ) . '</a>';
			 array_unshift( $links, $settings_link );
		}
		return $links;
	}
}

/* Function creates other links on admin page. */
if ( ! function_exists( 'twttr_links' ) ) {
	function twttr_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$links[]	=	'<a href="admin.php?page=twitter.php">' . __( 'Settings','twitter' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/twitter-plugin/faq/" target="_blank">' . __( 'FAQ','twitter' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support','twitter' ) . '</a>';
		}
		return $links;
	}
}

/* Function adds language files */
if ( ! function_exists( 'twttr_plugin_init' ) ) {
	function twttr_plugin_init() {
		load_plugin_textdomain( 'twitter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'twttr_plugin_version_check' ) ) {
	function twttr_plugin_version_check() {
		global $wp_version;
		$plugin_data	=	get_plugin_data( __FILE__, false );
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( __FILE__ );
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $plugin_data['Name'] . " </strong> " . __( 'requires', 'twitter' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'twitter') . "<br /><br />" . __( 'Back to the WordPress', 'twitter') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'twitter') . "</a>." );
			}
		}
	}
}

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'twttr_admin_head' ) ) {
	function twttr_admin_head() {
		global $wp_version;
		if ( $wp_version < 3.8 )
			wp_enqueue_style( 'twttrStylesheet', plugins_url( 'css/style_wp_before_3.8.css', __FILE__ ) );	
		else
			wp_enqueue_style( 'twttrStylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

if ( ! function_exists('twttr_admin_js') ) {
	function twttr_admin_js() {
		if ( isset( $_GET['page'] ) && "twitter.php" == $_GET['page'] ) {
			/* add notice about changing in the settings page */
			?>
			<script type="text/javascript">
				(function($) {
					$(document).ready( function() {
						$( '#twttr_settings_form input' ).bind( "change click select", function() {
							if ( $( this ).attr( 'type' ) != 'submit' ) {
								$( '.updated.fade' ).css( 'display', 'none' );
								$( '#twttr_settings_notice' ).css( 'display', 'block' );
							};
						});
						$( '#twttr_settings_form select' ).bind( "change", function() {
								$( '.updated.fade' ).css( 'display', 'none' );
								$( '#twttr_settings_notice' ).css( 'display', 'block' );
						});
					});
				})(jQuery);
			</script>
		<?php }
	}
}

/* Function for delete options */
if ( ! function_exists( 'twttr_delete_options' ) ) {
	function twttr_delete_options() {
		delete_option( 'twttr_options' );
		delete_site_option( 'twttr_options' );
	}
}

add_action( 'admin_menu', 'twttr_add_pages' );
add_action( 'init', 'twttr_plugin_init' );
/* Call register settings function */
add_action( 'init', 'twttr_settings' );
add_action( 'admin_init', 'twttr_settings' );
add_action( 'admin_init', 'twttr_plugin_version_check' );
add_action( 'admin_enqueue_scripts', 'twttr_admin_head' );
add_action( 'wp_enqueue_scripts', 'twttr_admin_head' );
add_action( 'admin_head', 'twttr_admin_js' );

add_shortcode( 'follow_me', 'twttr_follow_me' );

add_filter( 'the_content', "twttr_twit" );
/* Adds "Settings" link to the plugin action page */
add_filter( 'plugin_action_links', 'twttr_action_links', 10, 2 );
/* Additional links on the plugin page */
add_filter( 'plugin_row_meta', 'twttr_links', 10, 2 );
add_filter( 'widget_text', 'do_shortcode' );

register_uninstall_hook( __FILE__, 'twttr_delete_options' );
?>