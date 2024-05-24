<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Prmbr_Settings_Tabs' ) ) {
	/**
	 * Class Prmbr_Settings_Tabs for display Settings Tabs
	 */
	class Prmbr_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename Name for plugin.
		 */
		public function __construct( $plugin_basename ) {
			global $prmbr_options, $prmbr_plugin_info;

			$tabs = array(
				'settings'    => array( 'label' => __( 'Settings', 'promobar' ) ),
				'misc'        => array( 'label' => __( 'Misc', 'promobar' ) ),
				'custom_code' => array( 'label' => __( 'Custom Code', 'promobar' ) ),
				'license'     => array( 'label' => __( 'License key', 'promobar' ) ),
			);

			parent::__construct(
				array(
					'plugin_basename' => $plugin_basename,
					'plugins_info'    => $prmbr_plugin_info,
					'prefix'          => 'prmbr',
					'default_options' => prmbr_default_options(),
					'options'         => $prmbr_options,
					'tabs'            => $tabs,
					'wp_slug'         => 'promobar',
					'link_key'        => 'd765697418cb3510ea536e47c1e26396',
					'link_pn'         => '196',
					'doc_link'        => 'https://bestwebsoft.com/documentation/promobar/promobar-user-guide/',

				)
			);

			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
		}

		/**
		 * Save plugin options to the database
		 *
		 * @access public
		 * @return array    The action results
		 */
		public function save_options() {
			$message = '';
			$notice  = '';
			$error   = '';
			if ( isset( $_POST['prmbr_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['prmbr_nonce_field'] ) ), 'prmbr_save_action' ) ) {

				$this->options['enable'] = isset( $_POST['prmbr_enable'] ) ? 1 : 0;

				update_option( 'prmbr_options', $this->options );

				$message = __( 'Settings saved.', 'promobar' );
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Display Promobar Settings tab
		 */
		public function tab_settings() { ?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Promobar Settings', 'promobar' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label"><?php esc_html_e( 'Promobar', 'promobar' ); ?></div>
			<table class="form-table">
				<tr>
					<th><?php esc_html_e( 'Promobar', 'promobar' ); ?></th>
					<td>
						<label>
							<input type="checkbox" value="1" class="bws_option_affect" data-affect-show=".prmbr_enable" name="prmbr_enable" <?php checked( $this->options['enable'] ); ?>/>
							<span class="bws_info"><?php esc_html_e( 'Enable to display a promo bar.', 'promobar' ); ?></span>
						</label>
					</td>
				</tr>
			</table>
			<?php $this->pro_block( 'prmbr_countdown_block' ); ?>
			<?php wp_nonce_field( 'prmbr_save_action', 'prmbr_nonce_field' ); ?>
			<?php
		}

		/**
		 * Display bws_pro_version block by its name
		 *
		 * @param   string $block_name  Functiona for display block.
		 * @param   array  $args        Args for function.
		 * @param   boll   $force       Hide or show block.
		 */
		public function pro_block( $block_name = '', $args = array(), $force = false ) {
			if ( ( ! $this->hide_pro_tabs || $force ) && function_exists( $block_name ) ) {
				?>
				<div class="bws_pro_version_bloc prmbr-pro-feature">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'promobar' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<div class="bws_pro_version">
							<?php $block_name( $args ); ?>
						</div>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
				<?php
			}
		}

		/**
		 * Display custom metabox
		 *
		 * @access public
		 */
		public function display_metabox() {
			?>
			<div class="postbox">
				<h3 class="hndle">
					<?php esc_html_e( 'Promobar Shortcode', 'promobar' ); ?>
				</h3>
				<div class="inside">
					<?php echo wp_kses_post( __( 'Add PromoBar to your page or post </br> using the following shortcode:', 'promobar' ) ); ?></br></br>
					<?php bws_shortcode_output( '[bws_promobar]' ); ?>
				</div>
				<div class="inside prmbr_shortcode_pro">
					<?php $this->pro_block( 'prmbr_shortcode' ); ?>
				</div>
				<div class="inside">
					<?php esc_html_e( 'If you would like to use this plugin on certain pages, please paste the following strings into the template source code:', 'promobar' ); ?>
					<br>
					<span class="bws_code">&nbsp;&#60;?php do_action( 'prmbr_box' ); ?&#62;&nbsp;</span>
				</div>
			</div>
			<?php
		}
	}
}
