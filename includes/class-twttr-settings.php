<?php
/**
 * Displays the content on the plugin settings page
 *
 * @since 2.54
 */

if ( ! class_exists( 'Twttr_Settings_Tabs' ) ) {
	class Twttr_Settings_Tabs extends Bws_Settings_Tabs {

		private $lang_codes;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $twttr_options, $twttr_plugin_info;

			if ( is_network_admin() ) {
				$tabs = array(
					'settings' => array( 'label' => __( 'Settings', 'twitter-pro' ) ),
					'misc'     => array( 'label' => __( 'Misc', 'twitter-pro' ) ),
					'license'  => array( 'label' => __( 'License Key', 'twitter-pro' ) ),
				);
			} else {
				$tabs = array(
					'settings'    => array( 'label' => __( 'Settings', 'twitter-pro' ) ),
					'display'     => array( 'label' => __( 'Display', 'twitter-pro' ) ),
					'misc'        => array( 'label' => __( 'Misc', 'twitter-pro' ) ),
					'custom_code' => array( 'label' => __( 'Custom Code', 'twitter-pro' ) ),
					'license'     => array( 'label' => __( 'License Key', 'twitter-pro' ) ),
				);
			}

			parent::__construct(
				array(
					'plugin_basename'    => $plugin_basename,
					'plugins_info'       => $twttr_plugin_info,
					'prefix'             => 'twttr',
					'default_options'    => twttr_get_options_default(),
					'options'            => $twttr_options,
					'is_network_options' => is_network_admin(),
					'tabs'               => $tabs,
					'wp_slug'            => 'twitter-plugin',
					'doc_link'           => 'https://bestwebsoft.com/documentation/bestwebsofts-twitter/bestwebsofts-twitter-user-guide/',
				)
			);

			if ( $this->is_multisite && ! $this->is_network_options ) {
				if ( $network_options = get_site_option( 'twttr_options' ) ) {
					if ( 'all' == $network_options['network_apply'] && 0 == $network_options['network_change'] ) {
						$this->change_permission_attr = ' readonly="readonly" disabled="disabled"';
					}
					if ( 'all' == $network_options['network_apply'] && 0 == $network_options['network_view'] ) {
						$this->forbid_view = true;
					}
				}
			}
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
			add_action( get_parent_class( $this ) . '_display_second_postbox', array( $this, 'display_second_postbox' ) );
			add_action( get_parent_class( $this ) . '_display_second_postbox', array( $this, 'display_third_postbox' ) );
			add_filter( get_parent_class( $this ) . '_additional_restore_options', array( $this, 'additional_restore_options' ) );

			$this->lang_codes = array(
				'af_ZA' => 'Afrikaans',
				'ar_AR' => 'العربية',
				'az_AZ' => 'Azərbaycan dili',
				'be_BY' => 'Беларуская',
				'bg_BG' => 'Български',
				'bn_IN' => 'বাংলা',
				'bs_BA' => 'Bosanski',
				'ca_ES' => 'Català',
				'cs_CZ' => 'Čeština',
				'cy_GB' => 'Cymraeg',
				'da_DK' => 'Dansk',
				'de_DE' => 'Deutsch',
				'el_GR' => 'Ελληνικά',
				'en_US' => 'English',
				'en_PI' => 'English (Pirate)',
				'eo_EO' => 'Esperanto',
				'es_CO' => 'Español (Colombia)',
				'es_ES' => 'Español (España)',
				'es_LA' => 'Español',
				'et_EE' => 'Eesti',
				'eu_ES' => 'Euskara',
				'fa_IR' => 'فارسی',
				'fb_LT' => 'Leet Speak',
				'fi_FI' => 'Suomi',
				'fo_FO' => 'Føroyskt',
				'fr_CA' => 'Français (Canada)',
				'fr_FR' => 'Français (France)',
				'fy_NL' => 'Frysk',
				'ga_IE' => 'Gaeilge',
				'gl_ES' => 'Galego',
				'gn_PY' => "Avañe'ẽ",
				'gu_IN' => 'ગુજરાતી',
				'he_IL' => 'עברית',
				'hi_IN' => 'हिन्दी',
				'hr_HR' => 'Hrvatski',
				'hu_HU' => 'Magyar',
				'hy_AM' => 'Հայերեն',
				'id_ID' => 'Bahasa Indonesia',
				'is_IS' => 'Íslenska',
				'it_IT' => 'Italiano',
				'ja_JP' => '日本語',
				'jv_ID' => 'Basa Jawa',
				'ka_GE' => 'ქართული',
				'kk_KZ' => 'Қазақша',
				'km_KH' => 'ភាសាខ្មែរ',
				'kn_IN' => 'ಕನ್ನಡ',
				'ko_KR' => '한국어',
				'ku_TR' => 'Kurdî',
				'la_VA' => 'lingua latina',
				'lt_LT' => 'Lietuvių',
				'lv_LV' => 'Latviešu',
				'mk_MK' => 'Македонски',
				'ml_IN' => 'മലയാളം',
				'mn_MN' => 'Монгол',
				'mr_IN' => 'मराठी',
				'ms_MY' => 'Bahasa Melayu',
				'nb_NO' => 'Norsk (bokmål)',
				'ne_NP' => 'नेपाली',
				'nl_BE' => 'Nederlands (België)',
				'nl_NL' => 'Nederlands',
				'nn_NO' => 'Norsk (nynorsk)',
				'pa_IN' => 'ਪੰਜਾਬੀ',
				'pl_PL' => 'Polski',
				'ps_AF' => 'پښتو',
				'pt_BR' => 'Português (Brasil)',
				'pt_PT' => 'Português (Portugal)',
				'ro_RO' => 'Română',
				'ru_RU' => 'Русский',
				'sk_SK' => 'Slovenčina',
				'sl_SI' => 'Slovenščina',
				'sq_AL' => 'Shqip',
				'sr_RS' => 'Српски',
				'sv_SE' => 'Svenska',
				'sw_KE' => 'Kiswahili',
				'ta_IN' => 'தமிழ்',
				'te_IN' => 'తెలుగు',
				'tg_TJ' => 'тоҷикӣ',
				'th_TH' => 'ภาษาไทย',
				'tl_PH' => 'Filipino',
				'tr_TR' => 'Türkçe',
				'uk_UA' => 'Українська',
				'ur_PK' => 'اردو',
				'uz_UZ' => "O'zbek",
				'vi_VN' => 'Tiếng Việt',
				'zh_CN' => '中文(简体)',
				'zh_HK' => '中文(香港)',
				'zh_TW' => '中文(台灣)',
			);
		}

		/**
		 * Save plugin options to the database
		 *
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			global $wpdb;
			$message = $notice = $error = '';

			if ( ! $this->forbid_view ) {
				$this->options['url_twitter'] = stripslashes( sanitize_text_field( $_REQUEST['twttr_url_twitter'] ) );
				$this->options['position']    = array();
				if ( isset( $_REQUEST['twttr_position'] ) && is_array( $_REQUEST['twttr_position'] ) ) {
					foreach ( $_REQUEST['twttr_position'] as $value ) {
						if ( in_array( $value, array( 'before', 'after' ) ) ) {
							$this->options['position'][] = $value;
						}
					}
				}
				$this->options['tweet_display']            = isset( $_REQUEST['twttr_tweet_display'] ) ? 1 : 0;
				$this->options['size']                     = ( isset( $_REQUEST['twttr_size'] ) && in_array( $_REQUEST['twttr_size'], array( 'deafult', 'large' ) ) ? $_REQUEST['twttr_size'] : 'default' );
				$this->options['lang_default']             = isset( $_REQUEST['twttr_lang_default'] ) ? 1 : 0;
				$this->options['lang']                     = ( isset( $_REQUEST['twttr_lang'] ) && array_key_exists( $_REQUEST['twttr_lang'], $this->lang_codes ) ) ? $_REQUEST['twttr_lang'] : $this->options['lang'];
				$this->options['tailoring']                = isset( $_REQUEST['twttr_tailoring'] ) ? 1 : 0;
				$this->options['url_of_twitter']           = ( isset( $_REQUEST['twttr_url_of_twitter'] ) && in_array( $_REQUEST['twttr_url_of_twitter'], array( 'page_url', 'home_url' ) ) ? $_REQUEST['twttr_url_of_twitter'] : 'page_title' );
				$this->options['text_option_twitter']      = ( isset( $_REQUEST['twttr_text_option_twitter'] ) && in_array( $_REQUEST['twttr_text_option_twitter'], array( 'page_title', 'custom' ) ) ? $_REQUEST['twttr_text_option_twitter'] : 'page_title' );
				$this->options['text_twitter']             = isset( $_REQUEST['twttr_text_twitter'] ) ? stripslashes( sanitize_text_field( $_REQUEST['twttr_text_twitter'] ) ) : $this->options['text_twitter'];
				$this->options['via_twitter']              = isset( $_REQUEST['twttr_via_twitter'] ) ? stripslashes( sanitize_text_field( $_REQUEST['twttr_via_twitter'] ) ) : $this->options['via_twitter'];
				$this->options['followme_display']         = isset( $_REQUEST['twttr_followme_display'] ) ? 1 : 0;
				$this->options['username_display']         = isset( $_REQUEST['twttr_username_display'] ) ? 1 : 0;
				if ( isset( $_REQUEST['twttr_display_option'] ) ) {
					$this->options['display_option'] = ( isset( $_REQUEST['twttr_display_option'] ) && in_array( $_REQUEST['twttr_display_option'], array( 'standart', 'custom' ) ) ? $_REQUEST['twttr_display_option'] : 'standart' );
				}
				$this->options['hashtag_display']     = isset( $_REQUEST['twttr_hashtag_display'] ) ? 1 : 0;
				$this->options['text_option_hashtag'] = ( isset( $_REQUEST['twttr_text_option_hashtag'] ) && in_array( $_REQUEST['twttr_text_option_hashtag'], array( 'page_title', 'custom' ) ) ? $_REQUEST['twttr_text_option_hashtag'] : 'page_title' );
				$this->options['text_hashtag']        = isset( $_REQUEST['twttr_text_hashtag'] ) ? stripslashes( sanitize_text_field( $_REQUEST['twttr_text_hashtag'] ) ) : $this->options['text_hashtag'];
				$this->options['url_option_hashtag']  = ( isset( $_REQUEST['twttr_url_option_hashtag'] ) && in_array( $_REQUEST['twttr_url_option_hashtag'], array( 'no_url', 'page_url', 'home_url' ) ) ? $_REQUEST['twttr_url_option_hashtag'] : 'no_url' );
				$this->options['mention_display']     = isset( $_REQUEST['twttr_mention_display'] ) ? 1 : 0;
				$this->options['tweet_to_mention']    = isset( $_REQUEST['twttr_tweet_to_mention'] ) ? stripslashes( sanitize_text_field( $_REQUEST['twttr_tweet_to_mention'] ) ) : $this->options['tweet_to_mention'];
				/* '\w' can not be used due to php 5.2.4 have bugs with cirillic symbols in preg_ functions */
				$this->options['tweet_to_mention']    = preg_replace( '~[^\d_a-zA-Z]~', '', $this->options['tweet_to_mention'] );
				$this->options['text_option_mention'] = ( isset( $_REQUEST['twttr_text_option_mention'] ) && in_array( $_REQUEST['twttr_text_option_mention'], array( 'page_title', 'custom' ) ) ? $_REQUEST['twttr_text_option_mention'] : 'page_title' );
				$this->options['text_mention']        = isset( $_REQUEST['twttr_text_mention'] ) ? stripslashes( sanitize_text_field( $_REQUEST['twttr_text_mention'] ) ) : $this->options['text_mention'];
				$this->options['enabled_roles']['unauthorized'] = isset( $_POST['twttr_unauthorized'] ) ? 1 : 0;
				if ( 'custom' != $this->options['display_option'] ) {
					$img_name                  =
						'large' == $this->options['size'] ?
						'twitter-follow' :
						'twitter-follow-small';
					$this->options['img_link'] = plugins_url( 'images/' . $img_name . '.png', dirname( __FILE__ ) );
				}
				/* "reccomended users" fields */
				$related = array(
					'related_twitter',
					'related_hashtag',
					'related_mention',
				);
				/* fields with values which can be listed with "," separator */
				$multipleValues = array_merge(
					array( 'hashtag_twitter', 'hashtag' ),
					$related
				);
				foreach ( $multipleValues as $field ) {
					$value           = stripslashes( sanitize_text_field( $_REQUEST[ 'twttr_' . $field ] ) );
					$value           = preg_replace( '~\s+~', '%', $value ); /* delete from fields all space symbols */
					$exploded_values = explode( ',', $value ); /*
					create an array of values */
					/* delete all punctuation symbols form values */
					foreach ( $exploded_values as $key => $value ) {
						$exploded_values[ $key ] = preg_replace( '~[[:punct:]]~u', '', $value );
					}
					$exploded_values = array_filter( $exploded_values ); /* delete all empty elements */
					if ( ! empty( $exploded_values ) ) {
						if ( in_array( $field, $related ) ) {/* 'related' fields could have 2 values max */
							$exploded_values = array_slice( $exploded_values, 0, 2 ); /* return only 2 first elements */
						}
						$this->options[ $field ] = implode( ',', $exploded_values );
					}
				}
				if ( isset( $_FILES['twttr_upload_file']['tmp_name'] ) && $_FILES['twttr_upload_file']['tmp_name'] != '' ) {
					$this->options['count_icon'] = $this->options['count_icon'] + 1;
				}
				if ( 2 < $this->options['count_icon'] ) {
					$this->options['count_icon'] = 1;
				}
				/*  Twitter Timeline options */
				if ( filter_var( $_REQUEST['twttr_twitter_url'], FILTER_VALIDATE_URL ) ) {
					$this->options['twitter_url'] = esc_url( $_REQUEST['twttr_twitter_url'] );
				} elseif ( 1 == $this->options['tweet_display'] ) {
					$error = __( 'Error: Invalid Twitter URL', 'twitter-pro' );
				}
				$this->options['timeline_theme']       = ( isset( $_REQUEST['twttr_timeline_theme'] ) && in_array( $_REQUEST['twttr_timeline_theme'], array( 'light', 'dark' ) ) ? $_REQUEST['twttr_timeline_theme'] : 'dark' );
				$this->options['timeline_language']    = ( isset( $_REQUEST['twttr_timeline_language'] ) && array( $_REQUEST['twttr_timeline_language'] ) ) ? $_REQUEST['twttr_timeline_language'] : $this->options['timeline_language'];
				$this->options['timeline_height']      = intval( $_REQUEST['twttr_timeline_height'] );
				$this->options['timeline_width']       = intval( $_REQUEST['twttr_timeline_width'] );

				$this->options['timeline_design'] = array( 'noheader', 'nofooter', 'noborders', 'transparent', 'noscrollbar' );
				if ( isset( $_REQUEST['twttr_timeline_design'] ) && is_array( $_REQUEST['twttr_timeline_design'] ) ) {
					foreach ( $_REQUEST['twttr_timeline_design'] as $value ) {
						if ( in_array( $value, $this->options['timeline_design']/*array( 'noheader', 'nofooter' , 'noborders', 'transparent', 'noscrollbar' )*/ ) ) {
							$key_for_value = array_search( $value, $this->options['timeline_design'] );
							unset( $this->options['timeline_design'][ $key_for_value ] );
							/*$this->options['timeline_design'][] = $value;*/
						}
					}
				}

				/* Form options */
				if ( isset( $_FILES['twttr_upload_file']['tmp_name'] ) && '' != $_FILES['twttr_upload_file']['tmp_name'] ) {
					if ( ! $this->upload_dir ) {
						$this->upload_dir = wp_upload_dir();
					}
					if ( ! $this->upload_dir['error'] ) {
						$twttr_cstm_mg_folder = $this->upload_dir['basedir'] . '/twitter-logo';
						if ( ! is_dir( $twttr_cstm_mg_folder ) ) {
							wp_mkdir_p( $twttr_cstm_mg_folder, 0755 );
						}
					}
					$max_image_width = $max_image_height = 100;
					$max_image_size  = 32 * 1024;
					$valid_types     = array( 'jpg', 'jpeg', 'png' );
					/* Checks is file download initiated by user */
					if ( isset( $_FILES['twttr_upload_file'] ) && 'custom' == $_REQUEST['twttr_display_option'] ) {
						/* Checking is allowed download file given parameters */
						if ( is_uploaded_file( $_FILES['twttr_upload_file']['tmp_name'] ) ) {
							$filename = $_FILES['twttr_upload_file']['tmp_name'];
							$ext      = substr( $_FILES['twttr_upload_file']['name'], 1 + strrpos( $_FILES['twttr_upload_file']['name'], '.' ) );
							if ( filesize( $filename ) > $max_image_size ) {
								$error = __( 'Error: File size > 32K', 'twitter-pro' );
							} elseif ( ! in_array( strtolower( $ext ), $valid_types ) ) {
								$error = __( 'Error: Invalid file type', 'twitter-pro' );
							} else {
								$size = GetImageSize( $filename );
								if ( ( $size ) && ( $size[0] <= $max_image_width ) && ( $size[1] <= $max_image_height ) ) {
									/*
									 If file satisfies requirements, we will move them from temp to your plugin folder and rename to 'twitter_ico' */
									/* Construction to rename downloading file */
									$namefile   = 'twitter-follow' . $this->options['count_icon'] . '.' . $ext;
									$uploadfile = $twttr_cstm_mg_folder . '/' . $namefile;
									if ( move_uploaded_file( $_FILES['twttr_upload_file']['tmp_name'], $uploadfile ) ) {
										if ( 'custom' == $this->options['display_option'] ) {
											$this->options['img_link'] = $this->upload_dir['baseurl'] . '/twitter-logo/twitter-follow' . $this->options['count_icon'] . '.' . $ext;
										}
									} else {
										$error = __( 'Error: Failed to move file', 'twitter-pro' );
									}
								} else {
									$error = __( 'Error: Check image width or height', 'twitter-pro' );
								}
							}
						} else {
							$error = __( 'Uploading Error: Check image properties', 'twitter-pro' );
						}
					}
				}
				if ( $this->is_network_options ) {
					if ( 'all' == $_REQUEST['twttr_network_apply'] ) {
						/* Get all blog ids */
						$old_blog = $wpdb->blogid;
						$blogids  = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
						foreach ( $blogids as $blog_id ) {
							switch_to_blog( $blog_id );
							if ( $old_options = get_option( 'twttr_options' ) ) {
								$this->options_blog = array_merge( $old_options, $this->options );
								update_option( 'twttr_options', $this->options_blog );
							} else {
								add_option( 'twttr_options', array_merge( $this->default_options, $this->options ) );
							}
						}
						switch_to_blog( $old_blog );
					}
					$this->options['network_apply']  = $_REQUEST['twttr_network_apply'];
					$this->options['network_view']   = isset( $_REQUEST['twttr_network_view'] ) ? 1 : 0;
					$this->options['network_change'] = isset( $_REQUEST['twttr_network_change'] ) ? 1 : 0;
					update_site_option( 'twttr_options', $this->options );
				} else {
					update_option( 'twttr_options', $this->options );
				}
				$message = __( 'Settings saved', 'twitter-pro' );
			}
			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() {
			global $twttr_languages;

			if ( ! $this->upload_dir ) {
				$this->upload_dir = wp_upload_dir();
			} ?>
			<h3 class="bws_tab_label"><?php _e( 'Twitter Settings', 'twitter-pro' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php if ( $this->forbid_view ) { ?>
				<div class="error inline bws_visible"><p><strong><?php _e( 'Notice:', 'twitter-pro' ); ?></strong> <strong><?php _e( 'According to Twitter Pro network settings, it is prohibited to view the plugin settings on this site.', 'twitter-pro' ); ?></strong></p></div>
			<?php } else { ?>
				<div class="bws_tab_sub_label twttr_general"><?php _e( 'General', 'twitter-pro' ); ?></div>
				<?php if ( ! empty( $this->change_permission_attr ) ) { ?>
					<div class="error inline bws_visible"><p><strong><?php _e( 'Notice:', 'twitter-pro' ); ?></strong> <strong><?php _e( 'According to Twitter Pro network settings, it is prohibited to change the plugin settings on this site.', 'twitter-pro' ); ?></strong></p></div>
				<?php } ?>
				<table class="form-table twttr_settings_form">
					<?php if ( $this->is_network_options ) { ?>
						<tr class="twttr_network_settings">
							<th><?php _e( 'Apply network settings', 'twitter-pro' ); ?></th>
							<td>
							<fieldset>
									<label><input<?php echo $this->change_permission_attr; ?>  type="radio" name="twttr_network_apply" value="all" 
										<?php
										if ( 'all' == $this->options['network_apply'] ) {
											echo 'checked="checked"';}
										?>
									 /> <?php _e( 'Apply to all sites and use by default', 'twitter-pro' ); ?> <p class="bws_info"><?php _e( 'All current settings on separate sites will be replaced.', 'twitter-pro' ); ?></p></label><br />
									<div class="bws_network_apply_all">
										<label><input<?php echo $this->change_permission_attr; ?>  type="checkbox" name="twttr_network_change" value="1" 
											<?php
											if ( 1 == $this->options['network_change'] ) {
												echo 'checked="checked"';}
											?>
										 /> <?php _e( 'Allow changing the settings on separate websites', 'twitter-pro' ); ?></label><br />
										<label><input<?php echo $this->change_permission_attr; ?>  type="checkbox" name="twttr_network_view" value="1" 
											<?php
											if ( 1 == $this->options['network_view'] ) {
												echo 'checked="checked"';}
											?>
										 /> <?php _e( 'Allow viewing the settings on separate websites', 'twitter-pro' ); ?></label>
									</div>
									<label><input<?php echo $this->change_permission_attr; ?>  type="radio" name="twttr_network_apply" value="default" 
										<?php
										if ( 'default' == $this->options['network_apply'] ) {
											echo 'checked="checked"';}
										?>
									 /> <?php _e( 'By default', 'twitter-pro' ); ?> <p class="bws_info"><?php _e( 'Settings will be applied to newly added websites by default.', 'twitter-pro' ); ?></p></label>
									<br />
									<label><input<?php echo $this->change_permission_attr; ?>  type="radio" name="twttr_network_apply" value="off" 
										<?php
										if ( 'off' == $this->options['network_apply'] ) {
											echo 'checked="checked"';}
										?>
									 /> <?php _e( 'Do not apply', 'twitter-pro' ); ?> <p class="bws_info"><?php _e( 'Change the settings on separate sites of the multisite only.', 'twitter-pro' ); ?></p></label>
								</fieldset>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<th><?php _e( 'Buttons', 'twitter-pro' ); ?></th>
						<td>
							<fieldset>
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_tweet_display" type="checkbox" value="1" <?php checked( 1, $this->options['tweet_display'] ); ?> /> <?php _e( 'Tweet', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_followme_display" type="checkbox" value="1" <?php checked( 1, $this->options['followme_display'] ); ?> /> <?php _e( 'Follow', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_hashtag_display" type="checkbox" value="1" <?php checked( 1, $this->options['hashtag_display'] ); ?> /> <?php _e( 'Hashtag', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_mention_display" type="checkbox" value="1" <?php checked( 1, $this->options['mention_display'] ); ?> /> <?php _e( 'Mention', 'twitter-pro' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Buttons Size', 'twitter-pro' ); ?></th>
						<td>
							<fieldset>
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_size" type="radio" value="default" <?php checked( 'default', $this->options['size'] ); ?> /> <?php _e( 'Small', 'twitter-pro' ); ?></label><br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_size" type="radio" value="large" <?php checked( 'large', $this->options['size'] ); ?> /> <?php _e( 'Large', 'twitter-pro' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Buttons Position', 'twitter-pro' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input<?php echo $this->change_permission_attr; ?> type="checkbox" name="twttr_position[]" value="before" 
									 <?php
										if ( in_array( 'before', $this->options['position'] ) ) {
											echo 'checked="checked"';}
										?>
									 />
									<?php _e( 'Before content', 'twitter-pro' ); ?>
								</label>
								<br>
								<label>
									<input<?php echo $this->change_permission_attr; ?> type="checkbox" name="twttr_position[]" value="after" 
									 <?php
										if ( in_array( 'after', $this->options['position'] ) ) {
											echo 'checked="checked"';}
										?>
									 />
									<?php _e( 'After content', 'twitter-pro' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Language', 'twitter-pro' ); ?></th>
						<td>
							<label><input<?php echo $this->change_permission_attr; ?> id="twttr_lang_default" name="twttr_lang_default" type="checkbox" value="1" <?php checked( 1, $this->options['lang_default'] ); ?> /> <?php _e( 'Automatic', 'twitter-pro' ); ?></label>
							<br />
							<select<?php echo $this->change_permission_attr; ?> name="twttr_lang" id="twttr_lang_choose" 
								<?php
								if ( 1 == $this->options['lang_default'] ) {
									echo 'style="display:none"'; }
								?>
							 >
								<?php foreach ( $this->lang_codes as $key => $val ) { ?>
									<option value="<?php echo $key; ?>" <?php selected( $key, $this->options['lang'] ); ?>><?php echo $val; ?></option>
								<?php } ?>
							</select>
							<div class="bws_info"><?php _e( 'Select the default language for Twitter button(-s).', 'twitter-pro' ); ?></div>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Twitter Tailoring', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> name="twttr_tailoring" type="checkbox" value="1" <?php checked( 1, $this->options['tailoring'] ); ?> />
							<span class="bws_info"><?php _e( 'Enable tailored suggestions from Twitter.', 'twitter-pro' ); ?> <a href="https://support.twitter.com/articles/20169421#" target="_blank"><?php _e( 'Learn More', 'twitter-pro' ); ?></a></span>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Non-Login Users', 'twitter-pro' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input<?php echo $this->change_permission_attr; ?> type="checkbox" name="twttr_unauthorized" class="twitter_role" value="1" <?php checked( ! empty( $this->options['enabled_roles']['unauthorized'] ) && is_user_logged_in() ); ?> <?php echo ! is_user_logged_in() ? 'disabled' : ''; ?> />

									<span class="bws_info"><?php _e( 'Enable buttons for non-login users.', 'twitter-pro' ); ?></span>
								</label>
							</fieldset>
						</td>
					</tr>
				</table>
				<div class="bws_tab_sub_label twttr_timelines_enabled"><?php _e( 'Twitter Timeline', 'twitter-pro' ); ?></div>
				<table class="form-table twttr_settings_form twttr_timelines_enabled" id="twttr_timelines">
					<tr>
						<th><?php _e( 'Twitter URL', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> name="twttr_twitter_url" type="text" value="<?php echo $this->options['twitter_url']; ?>" maxlength="250" />
							<div class="bws_info"><?php _e( 'Enter Twitter account URL to display the timeline', 'twitter-pro' ); ?>.</div>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Theme', 'twitter-pro' ); ?></th>
						<td>
							<select<?php echo $this->change_permission_attr; ?> name="twttr_timeline_theme">
								<option value="light" <?php selected( 'light', $this->options['timeline_theme'] ); ?>><?php _e( 'Light', 'twitter-pro' ); ?></option>
								<option value="dark" <?php selected( 'dark', $this->options['timeline_theme'] ); ?>><?php _e( 'Dark', 'twitter-pro' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Language', 'twitter-pro' ); ?></th>
						<td>
							<select<?php echo $this->change_permission_attr; ?> id="twttr_timeline_language" name="twttr_timeline_language">
								<?php foreach ( $twttr_languages as $code => $name ) { ?>
									<option value="<?php echo $code; ?>" <?php selected( $this->options['timeline_language'], $code ); ?>><?php echo $name; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Height', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> class="small-text" name="twttr_timeline_height" type="number" value="<?php echo $this->options['timeline_height']; ?>" min="200"  step="1" />
							<p class="bws_info"><?php _e( 'Set a fixed height of the timeline block. The height parameter doesn\'t work when a "Number Of Tweets" is set to 0.', 'twitter-pro' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Width', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> class="small-text" name="twttr_timeline_width" type="number" value="<?php echo $this->options['timeline_width']; ?>" min="0"  step="1" />
							<p class="bws_info"><?php _e( 'Set the maximum width of the timeline block. Set 0 to automatically determine the width.', 'twitter-pro' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Timeline Design Elements', 'twitter-pro' ); ?></th>
						<td>
							<fieldset>
								<label><input<?php echo $this->change_permission_attr; ?>
									name="twttr_timeline_design[]" type="checkbox" value="noheader" 
									<?php
									if ( ! in_array( 'noheader', $this->options['timeline_design'] ) ) {
										echo 'checked="checked"';}
									?>
									/> <?php _e( 'Header', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?>
									name="twttr_timeline_design[]" type="checkbox" value="nofooter" 
									<?php
									if ( ! in_array( 'nofooter', $this->options['timeline_design'] ) ) {
										echo 'checked="checked"';}
									?>
									/> <?php _e( 'Footer', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?>
									name="twttr_timeline_design[]" type="checkbox" value="noborders" 
									<?php
									if ( ! in_array( 'noborders', $this->options['timeline_design'] ) ) {
										echo 'checked="checked"';}
									?>
									/> <?php _e( 'Borders', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_timeline_design[]" type="checkbox" value="transparent" 
									<?php
									if ( ! in_array( 'transparent', $this->options['timeline_design'] ) ) {
										echo 'checked="checked"';}
									?>
								/> <?php _e( 'Theme', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_timeline_design[]" type="checkbox" value="noscrollbar" 
									<?php
									if ( ! in_array( 'noscrollbar', $this->options['timeline_design'] ) ) {
										echo 'checked="checked"';}
									?>
								 /> <?php _e( 'Scrollbar', 'twitter-pro' ); ?></label>
							</fieldset>
						</td>
					</tr>
				</table>
				<div class="bws_tab_sub_label twttr_tweet_enabled"><?php _e( 'Tweet Button', 'twitter-pro' ); ?></div>
				<table class="form-table twttr_settings_form twttr_tweet_enabled">
					<tr>
						<th><?php _e( 'Share URL', 'twitter-pro' ); ?></th>
						<td>
							<fieldset>
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_url_of_twitter" type="radio" value="page_url" <?php checked( 'page_url', $this->options['url_of_twitter'] ); ?> /> <?php _e( 'Current page', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_url_of_twitter" type="radio" value="home_url" <?php checked( 'home_url', $this->options['url_of_twitter'] ); ?> /> <?php _e( 'Home page', 'twitter-pro' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th>
							<?php _e( 'Tweet Text', 'twitter-pro' ); ?>
						</th>
						<td>
							<fieldset>
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_text_option_twitter" type="radio" value="page_title" <?php checked( 'page_title', $this->options['text_option_twitter'] ); ?> /> <?php _e( 'Current page title', 'twitter-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="twttr_text_option_twitter" type="radio" value="custom" <?php checked( 'custom', $this->options['text_option_twitter'] ); ?> /> <?php _e( 'Custom', 'twitter-pro' ); ?></label>
								<br />
								<textarea<?php echo $this->change_permission_attr; ?> class="twttr_custom_input" name="twttr_text_twitter"><?php echo $this->options['text_twitter']; ?></textarea>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Via', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> name="twttr_via_twitter" type="text" value="<?php echo $this->options['via_twitter']; ?>" maxlength="250" />
							<div class="bws_info"><?php _e( 'Enter username for Tweet via parameter.', 'twitter-pro' ); ?></div>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Recommend', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> name="twttr_related_twitter" type="text" value="<?php echo $this->options['related_twitter']; ?>" maxlength="250" />
							<div class="bws_info"> <?php _e( 'Enter usernames of someone you recommend (maximum 2 allowed). For example: bestwebsoft, WordPress.', 'twitter-pro' ); ?></div>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Hashtag', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> name="twttr_hashtag_twitter" type="text" value="<?php echo $this->options['hashtag_twitter']; ?>" maxlength="512" />
							<div class="bws_info"> <?php _e( 'Enter one or multiple hashtags for your tweet. For example: bestwebsoft, WordPress, etc.', 'twitter-pro' ); ?></div>
						</td>
					</tr>
				</table>
				<div class="bws_tab_sub_label twttr_follow_enabled"><?php _e( 'Follow Button', 'twitter-pro' ); ?></div>
				<table class="form-table twttr_settings_form twttr_follow_enabled">
					<tr>
						<th><?php _e( 'Twitter username', 'twitter-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> name="twttr_url_twitter" type="text" value="<?php echo $this->options['url_twitter']; ?>" maxlength="19" />
							<div class="bws_info"><?php printf( __( 'Enter your Twitter account username or %1$s create a new one %2$s.', 'twitter-pro' ), '<a target="_blank" href="https://twitter.com/signup">', '</a>' ); ?></div>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Follow Button Image', 'twitter-pro' ); ?></th>
						<td>
							<?php if ( scandir( $this->upload_dir['basedir'] ) && is_writable( $this->upload_dir['basedir'] ) ) { ?>
								<fieldset>
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_display_option" type="radio" value="standart" <?php checked( 'standart', $this->options['display_option'] ); ?> /> <?php _e( 'Default', 'twitter-pro' ); ?></label>
									<br />
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_display_option" type="radio" value="custom" <?php checked( 'custom', $this->options['display_option'] ); ?> /> <?php _e( 'Custom', 'twitter-pro' ); ?></label>
								</fieldset>
								<?php
							} else {
								$no_upload_permission = true;
								printf( __( 'To use custom image, You need to setup permissions to upload directory of your site - %s', 'twitter-pro' ), $this->upload_dir['basedir'] );
							}
							?>
						</td>
					</tr>
						<?php if ( ! isset( $no_upload_permission ) ) { ?>
							<tr class="twttr_display_option_custom">
								<th></th>
								<td>
									<img src="<?php echo $this->options['img_link']; ?>" />
									<input type="hidden" name="twttr_img_link" value="<?php echo $this->options['img_link']; ?>" />
								</td>
							</tr>
							<tr class="twttr_display_option_custom">
								<th></th>
								<td>
									<input<?php echo $this->change_permission_attr; ?> type="file" name="twttr_upload_file" />
									<div class="bws_info"><?php printf( __( 'Upload image with the %1$s dimensions, JPG, JPEG or PNG formats (maximum file size - %2$s).', 'twitter-pro' ), '100×100px', '32kb' ); ?></div>
								</td>
							</tr>
						<?php } ?>
						<tr class="twttr_display_option_standart">
							<th><?php _e( 'Show Follow Button', 'twitter-pro' ); ?></th>
							<td>
								<fieldset>
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_username_display" type="checkbox" value="1" <?php checked( 1, $this->options['username_display'] ); ?> /> <?php _e( 'Username', 'twitter-pro' ); ?></label>
									<br/>
								</fieldset>
							</td>
						</tr>
					</table>
					<div class="bws_tab_sub_label twttr_hashtag_enabled"><?php _e( 'Hashtag Button', 'twitter-pro' ); ?></div>
					<table class="form-table twttr_settings_form twttr_hashtag_enabled">
						<tr>
							<th><?php _e( 'Hashtag', 'twitter-pro' ); ?></th>
							<td>
								<input<?php echo $this->change_permission_attr; ?> name="twttr_hashtag" type="text" value="<?php echo $this->options['hashtag']; ?>" maxlength="512" />
								<div class="bws_info"><?php _e( 'Enter one or multiple hashtags for your tweet. For example: bestwebsoft, WordPress, etc.', 'twitter-pro' ); ?></div>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Share URL', 'twitter-pro' ); ?></th>
							<td>
								<fieldset>
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_url_option_hashtag" type="radio" value="no_url" <?php checked( 'no_url', $this->options['url_option_hashtag'] ); ?> /> <?php _e( 'None', 'twitter-pro' ); ?></label>
									<br />
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_url_option_hashtag" type="radio" value="page_url" <?php checked( 'page_url', $this->options['url_option_hashtag'] ); ?> /> <?php _e( 'Current page', 'twitter-pro' ); ?></label>
									<br />
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_url_option_hashtag" type="radio" value="home_url" <?php checked( 'home_url', $this->options['url_option_hashtag'] ); ?> /> <?php _e( 'Home page', 'twitter-pro' ); ?></label>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Tweet Text', 'twitter-pro' ); ?></th>
							<td>
							<fieldset>
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_text_option_hashtag" type="radio" value="page_title" <?php checked( 'page_title', $this->options['text_option_hashtag'] ); ?> /> <?php _e( 'Current page title', 'twitter-pro' ); ?></label>
									<br />
									<label> <input<?php echo $this->change_permission_attr; ?> name="twttr_text_option_hashtag" type="radio" value="custom" <?php checked( 'custom', $this->options['text_option_hashtag'] ); ?> /> <?php _e( 'Custom', 'twitter-pro' ); ?></label>
									<br />
									<textarea<?php echo $this->change_permission_attr; ?> class="twttr_custom_input" name="twttr_text_hashtag"><?php echo $this->options['text_hashtag']; ?></textarea>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Recommend', 'twitter-pro' ); ?></th>
							<td>
								<input<?php echo $this->change_permission_attr; ?> name="twttr_related_hashtag" type="text" value="<?php echo $this->options['related_hashtag']; ?>" maxlength="250" />
								<div class="bws_info"> <?php _e( 'Enter usernames of someone you recommend (maximum 2 allowed). For example: bestwebsoft, WordPress.', 'twitter-pro' ); ?></div>
							</td>
						</tr>
					</table>
					<div class="bws_tab_sub_label twttr_mention_enabled"><?php _e( 'Mention Button', 'twitter-pro' ); ?></div>
					<table class="form-table twttr_settings_form twttr_mention_enabled">
						<tr>
							<th>
								<?php _e( 'Tweet to', 'twitter-pro' ); ?>
							</th>
							<td>
								<input<?php echo $this->change_permission_attr; ?> name="twttr_tweet_to_mention" type="text" value="<?php echo $this->options['tweet_to_mention']; ?>" maxlength="250" />
								<div class="bws_info"> <?php _e( 'Enter username of someone you want to mention.', 'twitter-pro' ); ?> <?php printf( __( 'For example, %s.', 'twitter-pro' ), 'bestwebsoft' ); ?></div>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Tweet Text', 'twitter-pro' ); ?></th>
							<td>
								<fieldset>
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_text_option_mention" type="radio" value="page_title" <?php checked( 'page_title', $this->options['text_option_mention'] ); ?> /> <?php _e( 'Current page', 'twitter-pro' ); ?></label>
									<br />
									<label><input<?php echo $this->change_permission_attr; ?> name="twttr_text_option_mention" type="radio" value="custom" <?php checked( 'custom', $this->options['text_option_mention'] ); ?> /> <?php _e( 'Custom', 'twitter-pro' ); ?></label>
									<br />
									<textarea<?php echo $this->change_permission_attr; ?> class="twttr_custom_input" name="twttr_text_mention"><?php echo $this->options['text_mention']; ?></textarea>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Recommend', 'twitter-pro' ); ?></th>
							<td>
								<input<?php echo $this->change_permission_attr; ?> name="twttr_related_mention" type="text" value="<?php echo $this->options['related_mention']; ?>" maxlength="250" />
								<div class="bws_info"><?php _e( 'Enter usernames of someone you recommend (maximum 2 allowed). For example: bestwebsoft, WordPress.', 'twitter-pro' ); ?></div>
							</td>
						</tr>
					</tr>
				</table>
				<?php
			}
		}

		/**
		 * Display custom metabox
		 *
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function display_metabox() {
			?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Twitter Shortcode', 'twitter-pro' ); ?>
				</h3>
				<div class="inside">
					<?php _e( 'Add Twitter button(-s) to your posts, pages, custom post types or widgets by using the following shortcode:', 'twitter-pro' ); ?>
					<?php bws_shortcode_output( '[twitter_buttons display=tweet,follow,hashtag,mention]' ); ?>
				</div>
				<div class="inside">
					<?php _e( 'Add Twitter timeline to your posts, pages, custom post types or widgets by using the following shortcode:', 'twitter-pro' ); ?>
					<?php bws_shortcode_output( '[twitter_timeline]' ); ?>
				</div>
			</div>
			<?php
		}

		public function display_second_postbox() {
			?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Twitter Buttons Preview', 'twitter-pro' ); ?>
				</h3>
				<div id="twttr_preview_wrap" class="inside">
					<div class="twttr_preview">
						<?php echo twttr_show_button( $this->options['tweet_display'], $this->options['followme_display'], $this->options['hashtag_display'], $this->options['mention_display'], $this->options ); ?>
					</div><!-- .twttr_preview -->
				</div>
			</div>
			<?php
		}

		public function display_third_postbox() {
			?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Twitter Timeline Preview', 'twitter-pro' ); ?>
				</h3>
				<div id="twttr_timeline_preview_wrap" class="inside">
					<div class="twttr_timeline_preview">
						<?php echo do_shortcode( '[twitter_timeline]' ); ?>
					</div><!-- .twttr_preview -->
				</div>
			</div>
			<?php
		}

		/**
		 *
		 */
		public function tab_display() {
			?>
			<h3 class="bws_tab_label"><?php _e( 'Display Settings', 'twitter-pro' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php
			twttr_show_js_tree();
		}

		/**
		 * Custom functions for "Restore plugin options to defaults"
		 *
		 * @access public
		 */
		public function additional_restore_options( $default_options ) {
			if ( function_exists( 'twttr_jstree_delete_db' ) ) {
				twttr_jstree_delete_db();
			}
			return $default_options;
		}
	}
}
