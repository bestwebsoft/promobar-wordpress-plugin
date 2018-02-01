<?php
/**
* Includes deprecated functions
*/

/**
 * @deprecated since 1.1.4
 * @todo remove after 01.07.2018
 */
if ( ! function_exists( 'prmbr_old_options' ) ) {
	function prmbr_old_options() {
		global $prmbr_options;

		if ( isset( $prmbr_options['position'] ) ) {
			$prmbr_options['position_desktop'] = $prmbr_options['position_tablet'] = $prmbr_options['position_mobile'] = $prmbr_options['position'];
			unset( $prmbr_options['position'] );
		}
		if ( isset( $prmbr_options['width_left'] ) ) {
			$prmbr_options['width_left_desktop'] = $prmbr_options['width_left_tablet'] = $prmbr_options['width_left_mobile'] = $prmbr_options['width_left'];
			unset( $prmbr_options['width_left'] );
		}
		if ( isset( $prmbr_options['width_right'] ) ) {
			$prmbr_options['width_right_desktop'] = $prmbr_options['width_right_tablet'] = $prmbr_options['width_right_mobile'] = $prmbr_options['width_right'];
			unset( $prmbr_options['width_right'] );
		}

		foreach ( array( 'desktop', 'tablet', 'mobile' ) as $position ) {
			if ( ! isset( $prmbr_options['unit_left_' . $position] ) ) {
				$prmbr_options['unit_left_' . $position] = '%';
			}
			if ( ! isset( $prmbr_options['unit_right_' . $position] ) ) {
				$prmbr_options['unit_right_' . $position] = '%';
			}
		}
	}
}
