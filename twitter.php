<?php
/*##
Plugin Name: Twitter by BestWebSoft
Plugin URI: http://bestwebsoft.com/donate/
Description: Plugin to add a link to the page author to twitter.
Author: BestWebSoft
Version: 2.44
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*
	@ Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

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

/* Add BWS menu */
if ( ! function_exists ( 'twttr_add_pages' ) ) {
	function twttr_add_pages() {
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		add_submenu_page( 'bws_plugins', __( 'Twitter Settings', 'twitter' ), 'Twitter', 'manage_options', 'twitter.php', 'twttr_settings_page' );
	}
}
/* end twttr_add_pages ##*/

/* Function for init */
if ( ! function_exists( 'twttr_init' ) ) {
	function twttr_init() {
		global $twttr_plugin_info;
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'twitter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		if ( empty( $twttr_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$twttr_plugin_info = get_plugin_data( __FILE__ );
		}

		/*## add general functions */
		require_once( dirname( __FILE__ ) . '/bws_menu/bws_functions.php' );
		
		bws_wp_version_check( plugin_basename( __FILE__ ), $twttr_plugin_info, '3.1' ); /* check compatible with current WP version ##*/

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "twitter.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) )
			twttr_settings();
	}
}

/*## Function for admin_init */
if ( ! function_exists( 'twttr_admin_init' ) ) {
	function twttr_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $twttr_plugin_info;

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '76', 'version' => $twttr_plugin_info["Version"] );
	}
}
/* end twttr_admin_init ##*/

/* Register settings for plugin */
if ( ! function_exists( 'twttr_settings' ) ) {
	function twttr_settings() {
		global $twttr_options, $twttr_plugin_info, $twttr_options_default;

		$twttr_options_default = array(
			'plugin_option_version' => $twttr_plugin_info["Version"],
			'url_twitter' 			=>	'admin',
			'display_option'		=>	'custom',
			'count_icon' 			=>	1,
			'img_link' 				=>	plugins_url( "images/twitter-follow.jpg", __FILE__ ),
			'position' 				=>	'before',
			'disable' 				=>	'0'
		);
		/* Install the option defaults */
		/* Get options from the database */
		if ( ! get_option( 'twttr_options' ) ) {
			if ( false !== get_option( 'twttr_options_array' ) ) {
				$old_options = get_option( 'twttr_options_array' );
				foreach ( $twttr_options_default as $key => $value ) {
					if ( isset( $old_options['twttr_' . $key] ) )
						$twttr_options_default[$key] = $old_options['twttr_' . $key];
				}
				delete_option( 'twttr_options_array' );
			}
			add_option( 'twttr_options', $twttr_options_default );
		}
		$twttr_options = get_option( 'twttr_options' );
		
		if ( ! isset( $twttr_options['plugin_option_version'] ) || $twttr_options['plugin_option_version'] != $twttr_plugin_info["Version"] ) {
			if ( '0' == $twttr_options['position'] )
				$twttr_options['position'] = 'after';
			elseif ( '1' == $twttr_options['position'] )
				$twttr_options['position'] = 'before';

			$twttr_options = array_merge( $twttr_options_default, $twttr_options );
			$twttr_options['plugin_option_version'] = $twttr_plugin_info["Version"];
			update_option( 'twttr_options', $twttr_options );
		}
	}
}
/* end twttr_settings */

/* Add Setting page */
if ( ! function_exists( 'twttr_settings_page' ) ) {
	function twttr_settings_page() {
		global $twttr_options, $wp_version, $twttr_plugin_info, $title, $twttr_options_default;
		$message = $error = "";
		$upload_dir = wp_upload_dir();
		$plugin_basename = plugin_basename( __FILE__ );

		if ( isset( $_REQUEST['twttr_form_submit'] ) && check_admin_referer( $plugin_basename, 'twttr_nonce_name' ) ) {
			$twttr_options['url_twitter']		=	stripslashes( esc_html( $_REQUEST['twttr_url_twitter'] ) );
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
				if ( ! $upload_dir["error"] ) {
					$twttr_cstm_mg_folder = $upload_dir['basedir'] . '/twitter-logo';
					if ( ! is_dir( $twttr_cstm_mg_folder ) ) {
						wp_mkdir_p( $twttr_cstm_mg_folder, 0755 );
					}
				}
				$max_image_width	=	100;
				$max_image_height	=	100;
				$max_image_size		=	32 * 1024;
				$valid_types 		=	array( 'jpg', 'jpeg' );
				/* Construction to rename downloading file */
				$new_name			=	'twitter-follow' . $twttr_options['count_icon'];
				$new_ext			=	'.jpg';
				$namefile			=	$new_name . $new_ext;
				/*$uploaddir			=	$_REQUEST['home'] . 'wp-content/plugins/twitter-plugin/images/'; /* The directory in which we will take the file: */
				$uploadfile			=	$twttr_cstm_mg_folder . '/' . $namefile;

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['upload_file'] ) && 'custom' == $_REQUEST['twttr_display_option'] )	{
					/* Checking is allowed download file given parameters */
					if ( is_uploaded_file( $_FILES['upload_file']['tmp_name'] ) ) {
						$filename	=	$_FILES['upload_file']['tmp_name'];
						$ext		=	substr( $_FILES['upload_file']['name'], 1 + strrpos( $_FILES['upload_file']['name'], '.' ) );
						if ( filesize( $filename ) > $max_image_size ) {
							$error = __( "Error: File size > 32K", 'twitter' );
						} elseif ( ! in_array( $ext, $valid_types ) ) {
							$error = __( "Error: Invalid file type", 'twitter' );
						} else {
							$size = GetImageSize( $filename );
							if ( ( $size ) && ( $size[0] <= $max_image_width ) && ( $size[1] <= $max_image_height ) ) {
								/* If file satisfies requirements, we will move them from temp to your plugin folder and rename to 'twitter_ico.jpg' */
								if ( move_uploaded_file( $_FILES['upload_file']['tmp_name'], $uploadfile ) ) {									
									if ( 'standart' == $twttr_options[ 'display_option' ] ) {
										$twttr_img_link	=	plugins_url( 'images/twitter-follow.jpg', __FILE__ );
									} else if ( 'custom' == $twttr_options['display_option'] ) {
										$twttr_img_link = $upload_dir['baseurl'] . '/twitter-logo/twitter-follow' . $twttr_options['count_icon'] . '.jpg';
									}
									$twttr_options['img_link'] = $twttr_img_link;
									update_option( "twttr_options", $twttr_options );
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

		/*## Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
			$twttr_options = $twttr_options_default;
			update_option( 'twttr_options', $twttr_options );
			$message = __( 'All plugin settings were restored.', 'twitter' );
		}		
		/* end ##*/

		/*## GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
		} /* end GO PRO ##*/ ?>
		<!-- general -->
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php echo $title; ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=twitter.php"><?php _e( 'Settings', 'twitter' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=twitter.php&amp;action=extra"><?php _e( 'Extra settings', 'twitter' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/twitter/faq" target="_blank"><?php _e( 'FAQ', 'twitter' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=twitter.php&amp;action=go_pro"><?php _e( 'Go PRO', 'twitter' ); ?></a>
			</h2>
			<!-- end general -->			
			<div class="updated fade" <?php if ( empty( $message ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="twttr_settings_notice" class="updated fade bws_settings_form_notice" style="display:none"><p><strong><?php _e( "Notice:", 'twitter' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'twitter' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php /*## check action */ if ( ! isset( $_GET['action'] ) ) {
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else { /* check action ##*/ ?>
					<form method='post' action="" enctype="multipart/form-data" id="twttr_settings_form" class="bws_settings_form">
						<table class="form-table">
							<tr valign="top">
								<th scope="row" colspan="2"><?php _e( 'Settings for the button "Follow Me"', 'twitter' ); ?>:</th>
							</tr>
							<tr valign="top">
								<th scope="row">
									<?php _e( "Enter your username", 'twitter' ); ?>
								</th>
								<td>
									<input name='twttr_url_twitter' type='text' value='<?php echo $twttr_options['url_twitter'] ?>' maxlength='250' /><br />
									<span class="bws_info"><?php _e( 'If you do not have Twitter account yet, you should create it using this link', 'twitter' ); ?> <a target="_blank" href="https://twitter.com/signup">https://twitter.com/signup</a> .</span><br />
									<span class="bws_info"><?php _e( 'Paste the shortcode &lsqb;follow_me&rsqb; into the necessary page or post to use the "Follow Me" button.', 'twitter' ); ?></span><br />
									<span class="bws_info"><?php _e( 'If you would like to use this button in some other place, please paste this line into the template source code', 'twitter' ); ?>	&#60;?php if ( function_exists( 'twttr_follow_me' ) ) echo twttr_follow_me(); ?&#62;</span>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<?php _e( "Choose display settings", 'twitter' ); ?>
								</th>
								<td>
									<?php if ( scandir( $upload_dir['basedir'] ) && is_writable( $upload_dir['basedir'] ) ) { ?>
										<select name="twttr_display_option" onchange="if ( this . value == 'custom' ) { getElementById ( 'twttr_display_option_custom' ) . style.display = 'block'; } else { getElementById ( 'twttr_display_option_custom' ) . style.display = 'none'; }">
											<option <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="standart"><?php _e( "Standard button", 'twitter' ); ?></option>
											<option <?php if ( 'custom' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="custom"><?php _e( "Custom button", 'twitter' ); ?></option>
										</select>
									<?php } else {
										echo __( "To use custom image You need to setup permissions to upload directory of your site", 'twitter' ) . " - " . $upload_dir['basedir'];
									} ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="twttr_display_option_custom" <?php if ( 'custom' == $twttr_options['display_option'] ) { echo ( 'style="display:block"' ); } else { echo ( 'style="display:none"' ); } ?>>
										<table>
											<th style="padding-left:0px;font-size:13px;">
												<?php _e( "Current image", 'twitter' ); ?>
											</th>
											<td>
												<img src="<?php echo $twttr_options['img_link']; ?>" />
											</td>
										</table>
										<table>
											<th style="padding-left:0px;font-size:13px;">
												<?php _e( '"Follow Me" image', 'twitter' ); ?>
											</th>
											<td>
												<input type="file" name="upload_file" /><br />
												<span class="bws_info"><?php _e( 'Image properties: max image width:100px; max image height:100px; max image size:32Kb; image types:"jpg", "jpeg".', 'twitter' ); ?></span>
											</td>
										</table>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row" colspan="2"><?php _e( 'Settings for the "Twitter" button', 'twitter' ); ?>:</th>
							</tr>
							<tr>
								<th><?php _e( 'Disable the "Twitter" button', 'twitter' ); ?></th>
								<td>
									<input type="checkbox" name="twttr_disable" value="1" <?php if ( 1 == $twttr_options["disable"] ) echo "checked=\"checked\""; ?> />
									<span class="bws_info"> <?php _e( 'The button "T" will not be displayed. Just the shortcode &lsqb;follow_me&rsqb; will work.', 'twitter' ); ?></span><br />
								</td>
							</tr>
							<tr>
								<th>
									<?php _e( 'The "Twitter" icon position', 'twitter' ); ?>
								</th>
								<td>
									<select name="twttr_position">
										<option value="before" <?php if ( 'before' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before', 'twitter' ); ?></option>
										<option value="after" <?php if ( 'after' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After', 'twitter' ); ?></option>
										<option value="after_and_before" <?php if ( 'after_and_before' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After', 'twitter' ); ?></option>
									</select>
									<span class="bws_info"><?php _e( 'By clicking this icon a user can add the article he/she likes to his/her Twitter page.', 'twitter' ); ?></span><br />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="hidden" name="twttr_form_submit" value="submit" />
									<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'twitter' ) ?>" />
								</td>
							</tr>
						</table>
						<?php wp_nonce_field( $plugin_basename, 'twttr_nonce_name' ); ?>
					</form>
					<!-- general -->
					<?php bws_form_restore_default_settings( $plugin_basename );
					bws_plugin_reviews_block( $twttr_plugin_info['Name'], 'twitter-plugin' );
				}
			} elseif ( 'extra' == $_GET['action'] ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">	
						<div class="bws_table_bg"></div>											
						<table class="form-table bws_pro_version">
							<tr valign="top">
								<td colspan="2">
									<?php _e( 'Please choose the necessary post types (or single pages) where Twitter button will be displayed:', 'twitter' ); ?>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<label>
										<input disabled="disabled" checked="checked" id="twttrpr_jstree_url" type="checkbox" name="twttrpr_jstree_url" value="1" />
										<?php _e( "Show URL for pages", 'twitter' );?>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<img src="<?php echo plugins_url( 'images/pro_screen_1.png', __FILE__ ); ?>" alt="<?php _e( "Example of the site's pages tree", 'twitter' ); ?>" title="<?php _e( "Example of the site's pages tree", 'twitter' ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'twitter' ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row" colspan="2">
									* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'twitter' ); ?>
								</th>
							</tr>				
						</table>	
					</div>
					<div class="bws_pro_version_tooltip">
						<div class="bws_info">
							<?php _e( 'Unlock premium options by upgrading to a PRO version.', 'twitter' ); ?> 
							<a href="http://bestwebsoft.com/products/twitter/?k=a8417eabe3c9fb0c2c5bed79e76de43c&pn=76&v=<?php echo $twttr_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Twitter Pro"><?php _e( 'Learn More', 'twitter' ); ?></a>				
						</div>
						<a class="bws_button" href="http://bestwebsoft.com/products/twitter/buy/?k=a8417eabe3c9fb0c2c5bed79e76de43c&pn=76&v=<?php echo $twttr_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Twitter Pro">
							<?php _e( 'Go', 'twitter' ); ?> <strong>PRO</strong>
						</a>	
						<div class="clear"></div>					
					</div>
				</div>
			<?php } elseif ( 'go_pro' == $_GET['action'] ) { 
				bws_go_pro_tab( $twttr_plugin_info, $plugin_basename, 'twitter.php', 'twitter-pro.php', 'twitter-pro/twitter-pro.php', 'twitter', 'a8417eabe3c9fb0c2c5bed79e76de43c', '76', isset( $go_pro_result['pro_plugin_is_activated'] ) ); 
			} ?>	
		</div>
		<!-- end general -->
	<?php }
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
		$permalink_post	=	get_permalink( $post->ID );
		$title_post		=	htmlspecialchars( urlencode( $post->post_title ) );
		if ( $title_post == 'your-post-page-title' )
			return $content;
		if ( 0 == $twttr_options['disable'] ) {
			$str = '<div class="twttr_button">
						<a href="http://twitter.com/share?url=' . $permalink_post . '&text=' . $title_post . '" target="_blank" title="' . __( 'Click here if you like this article.', 'twitter' ) . '">
							<img src="' . plugins_url( 'images/twitt.gif', __FILE__ ) . '" alt="Twitt" />
						</a>
					</div>';
			if ( 'before' == $twttr_options['position'] ) {
				return $str . $content;
			} elseif ( 'after' == $twttr_options['position'] ) {
				return $content . $str;
			} else {
				return $str . $content . $str;
			}
		} else {
			return $content;
		}
	}
}

/*## Functions creates other links on plugins page. */
if ( ! function_exists( 'twttr_action_links' ) ) {
	function twttr_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row */
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=twitter.php">' . __( 'Settings', 'twitter' ) . '</a>';
				array_unshift( $links, $settings_link );
			}			
		}
		return $links;
	}
}

if ( ! function_exists( 'twttr_links' ) ) {
	function twttr_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=twitter.php">' . __( 'Settings','twitter' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/twitter-plugin/faq/" target="_blank">' . __( 'FAQ','twitter' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support','twitter' ) . '</a>';
		}
		return $links;
	}
}

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'twttr_admin_head' ) ) {
	function twttr_admin_head() {
		if ( isset( $_GET['page'] ) && "twitter.php" == $_GET['page'] )
			wp_enqueue_script( 'twttr_script', plugins_url( 'js/script.js', __FILE__ ) );
	}
}
/* end twttr_admin_head ##*/

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'twttr_wp_head' ) ) {
	function twttr_wp_head() {
		wp_enqueue_style( 'twttr_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/*## add banner on plugins page */
if ( ! function_exists ( 'twttr_plugin_banner' ) ) {
	function twttr_plugin_banner() {
		global $hook_suffix;	
		if ( 'plugins.php' == $hook_suffix ) {  
			global $twttr_plugin_info;
			bws_plugin_banner( $twttr_plugin_info, 'twttr', 'twitter', '137342f0aa4b561cf7f93c190d95c890', '76', '//ps.w.org/twitter-plugin/assets/icon-128x128.png' );
		}
	}
}

/* Function for delete options */
if ( ! function_exists( 'twttr_delete_options' ) ) {
	function twttr_delete_options() {
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$all_plugins = get_plugins();
		if ( ! array_key_exists( 'bws-social-buttons/bws-social-buttons.php', $all_plugins ) ) {
			if ( ! array_key_exists( 'twitter-pro/twitter-pro.php', $all_plugins ) ) {
				/* delete custom images if no PRO version */
				$upload_dir = wp_upload_dir();
				$twttr_cstm_mg_folder = $upload_dir['basedir'] . '/twitter-logo/';
				if ( is_dir( $twttr_cstm_mg_folder ) ) {
					$twttr_cstm_mg_files = scandir( $twttr_cstm_mg_folder );
					foreach ( $twttr_cstm_mg_files as $value ) {
						@unlink ( $twttr_cstm_mg_folder . $value );
					}
					@rmdir( $twttr_cstm_mg_folder );
				}
			}				
			delete_option( 'twttr_options' );
		}
	}
}
/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'twttr_add_pages' );
/* Initialization ##*/
add_action( 'init', 'twttr_init' );
/*## admin_init */
add_action( 'admin_init', 'twttr_admin_init' );
/* Adding scripts */
add_action( 'admin_enqueue_scripts', 'twttr_admin_head' );
/* Adding stylesheets ##*/
add_action( 'wp_enqueue_scripts', 'twttr_wp_head' );
/* Adding plugin buttons */
add_shortcode( 'follow_me', 'twttr_follow_me' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', "twttr_twit" );
/*## Additional links on the plugin page */
add_filter( 'plugin_action_links', 'twttr_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'twttr_links', 10, 2 );
/* Adding banner */
add_action( 'admin_notices', 'twttr_plugin_banner' );
/* Plugin uninstall function */
register_uninstall_hook( __FILE__, 'twttr_delete_options' );
/* end ##*/