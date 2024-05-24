<?php
/**
 * Dipslay List table for probobars
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Prmbr_List_Table for display list tabel with promobars
 */
class Prmbr_List_Table extends WP_List_Table {

	/**
	 * Declare constructor
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'promobar',
				'plural'   => 'promobars',
				'ajax'     => true,
			)
		);
	}

	/**
	 * Declare column renderer
	 *
	 * @param array  $item        Row (key, value array).
	 * @param string $column_name String (key).
	 * @return HTML
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'shortcode':
				bws_shortcode_output( '[bws_promobar]' );
				break;
			case 'datetime':
				return '';
				break;
			case 'title':
				return $item[ $column_name ];
				break;
			default:
				return print_r( $item, true );
				break;
		}
	}

	/**
	 * Render column with actions
	 *
	 * @param array $item - row (key, value array).
	 * @return HTML
	 */
	public function column_title( $item ) {
		$actions = array(
			'edit' => sprintf( '<a href="?page=promobar-new.php&prmbr_id=%d">%s</a>', $item['id'], __( 'Edit', 'promobar' ) ),
		);

		$title = $item['title'];

		return sprintf(
			'<strong><a href="?page=promobar-new.php&prmbr_id=%d">%s</strong></a>%s',
			$item['id'],
			$title,
			$this->row_actions( $actions )
		);
	}

	/**
	 * Checkbox column renders
	 *
	 * @param array $item - row (key, value array).
	 * @return HTML
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="" value="" />'
		);
	}

	/**
	 * Return promobars to display in table
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'title'     => __( 'Title', 'promobar' ),
			'shortcode' => __( 'Shortcode', 'promobar' ),
			'datetime'  => __( 'Date', 'promobar' ),
		);
		return $columns;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which - only for bottom.
	 */
	public function display_tablenav( $which ) {
		global $prmbr_options, $prmbr_plugin_info, $wp_version;
		if ( ! bws_hide_premium_options_check( $prmbr_options ) ) {
			if ( 'bottom' === $which ) {
				?>
			<div class="bws_pro_version_bloc prmbr-pro-feature">
				<div class="bws_pro_version_table_bloc">
					<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'promobar' ); ?>"></button>
					<div class="bws_table_bg"></div>
					<div class="bws_pro_version">
				<?php } ?>
						<div class="tablenav <?php echo esc_attr( $which ); ?>">
							<div class="alignleft actions bulkactions">
								<?php $this->bulk_actions( $which ); ?>
							</div>
							<?php $this->pagination( $which ); ?>
							<br class="clear" />
						</div>
					</div>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="<?php echo esc_url( $prmbr_plugin_info['PluginURI'] ); ?>?k=fa164f00821ed3a87e6f78cb3f5c277b&amp;pn=143&amp;v=<?php echo esc_attr( $prmbr_plugin_info['Version'] ); ?>&amp;wp_v=<?php echo esc_attr( $wp_version ); ?>" target="_blank" title="<?php echo esc_html( $prmbr_plugin_info['Name'] ); ?>"><?php esc_html_e( 'Upgrade to Pro', 'promobar' ); ?></a>
					<div class="clear"></div>
				</div>
			</div>
			<?php
		}
	}


	/**
	 * Return array of bulk actions if has any
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'promobar' ),
		);
		return $actions;
	}

	/**
	 * Get rows from database and prepare them to be showed in table
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/* Show all slider categories */
		$this->items[] = array(
			'id'    => 1,
			'title' => 'Promobar',
		);

	}
}
