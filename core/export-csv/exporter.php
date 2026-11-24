<?php

namespace MetCraft\Core\Export_CSV;

defined( 'ABSPATH' ) || exit;

/**
 * CSV Export for MetCraft form entries.
 * Exports form entries to CSV format.
 */
class Exporter {

	/**
	 * Export entries for a form to CSV.
	 *
	 * @param int $form_id Form ID.
	 * @return string CSV content.
	 */
	public static function export( $form_id ) {
		$form_id = absint( $form_id );
		$entries = \MetCraft\Core\Entries\Base::instance()->get_entries( $form_id );

		if ( empty( $entries ) ) {
			return '';
		}

		// Get all unique field keys across entries
		$all_keys = [];
		foreach ( $entries as $entry ) {
			foreach ( (array) $entry['data'] as $key => $val ) {
				if ( ! in_array( $key, $all_keys, true ) ) {
					$all_keys[] = $key;
				}
			}
		}

		// Build CSV headers
		$headers = [ 'Entry ID', 'Submitted At', 'IP Address' ];
		$headers = array_merge( $headers, $all_keys );

		$output = fopen( 'php://temp', 'r+' );
		fputcsv( $output, $headers );

		foreach ( $entries as $entry ) {
			$row = [
				$entry['id'],
				$entry['date'],
				$entry['ip'],
			];

			foreach ( $all_keys as $key ) {
				$value = isset( $entry['data'][ $key ] ) ? $entry['data'][ $key ] : '';
				if ( is_array( $value ) ) {
					$value = implode( ', ', $value );
				}
				$row[] = $value;
			}

			fputcsv( $output, $row );
		}

		rewind( $output );
		$csv = stream_get_contents( $output );
		fclose( $output );

		return $csv;
	}

	/**
	 * Output CSV as download.
	 *
	 * @param int $form_id Form ID.
	 * @param string $filename Filename.
	 */
	public static function download( $form_id, $filename = '' ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'metcraft' ) );
		}

		$form_id = absint( $form_id );
		$csv = self::export( $form_id );

		if ( empty( $filename ) ) {
			$form = get_post( $form_id );
			$filename = sanitize_file_name( $form->post_title ?? 'entries' ) . '-entries.csv';
		}

		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: 0' );

		echo $csv;
		exit;
	}

}
