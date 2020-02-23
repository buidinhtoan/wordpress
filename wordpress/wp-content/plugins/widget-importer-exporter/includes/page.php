<?php
/**
 * Admin Page Functions
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2017, ChurchThemes.com
 * @link       https://churchthemes.com/plugins/widget-importer-exporter/
 * @license    GPLv2 or later
 * @since      0.1
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add import/export page under Tools
 *
 * @since 0.1
 */
function wie_add_import_export_page() {

	// Add page.
	$page_hook = add_management_page(
		esc_html__( 'Widget Importer & Exporter', 'widget-importer-exporter' ), // Page title.
		esc_html__( 'Widget Importer & Exporter', 'widget-importer-exporter' ), // Menu title.
		'edit_theme_options', // Capability (can manage Appearance > Widgets).
		'widget-importer-exporter', // Menu Slug.
		'wie_import_export_page_content' // Callback for displaying page content.
	);

}

add_action( 'admin_menu', 'wie_add_import_export_page' );

/**
 * Import/export page content
 *
 * @since 0.1
 */
function wie_import_export_page_content() {

	?>
	<div class="wrap">

		<h2><?php esc_html_e( 'Widget Importer & Exporter', 'widget-importer-exporter' ); ?></h2>

		<?php

		// Show import results if have them.
		if ( wie_have_import_results() ) {

			wie_show_import_results();

			// Don't show content below.
			return;

		}

		?>

		<h3 class="title"><?php echo esc_html_x( 'Import Widgets', 'heading', 'widget-importer-exporter' ); ?></h3>

		<p>

			<?php

			echo wp_kses(
				__( 'Please select a <b>.wie</b> file generated by this plugin.', 'widget-importer-exporter' ),
				array(
					'b' => array(),
				)
			);

			?>

		</p>

		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'wie_import', 'wie_import_nonce' ); ?>
			<input type="file" name="wie_import_file" id="wie-import-file"/>
			<?php submit_button( esc_html_x( 'Import Widgets', 'button', 'widget-importer-exporter' ) ); ?>
		</form>

		<?php if ( ! empty( $wie_import_results ) ) : ?>
			<p id="wie-import-results">
				<?php echo wp_kses_post( $wie_import_results ); ?>
			</p>
			<br/>
		<?php endif; ?>

		<h3 class="title"><?php echo esc_html_x( 'Export Widgets', 'heading', 'widget-importer-exporter' ); ?></h3>

		<p>
			<?php
			echo wp_kses(
				__( 'Click below to generate a <b>.wie</b> file for all active widgets.', 'widget-importer-exporter' ),
				array(
					'b' => array(),
				)
			);
			?>
		</p>

		<p class="submit">

			<a href="<?php echo esc_url( admin_url( basename( $_SERVER['PHP_SELF'] ) . '?page=' . $_GET['page'] . '&export=1&wie_export_nonce=' . wp_create_nonce( 'wie_export' ) ) ); ?>" id="wie-export-button" class="button button-primary">
				<?php echo esc_html_x( 'Export Widgets', 'button', 'widget-importer-exporter' ); ?>
			</a>

		</p>

	</div>

	<?php

}

/**
 * Have import results to show?
 *
 * @since 0.3
 * @global string $wie_import_results
 * @return bool True if have import results to show
 */
function wie_have_import_results() {

	global $wie_import_results;

	if ( ! empty( $wie_import_results ) ) {
		return true;
	}

	return false;

}

/**
 * Show import results
 *
 * This is shown in place of import/export page's regular content.
 *
 * @since 0.3
 * @global string $wie_import_results
 */
function wie_show_import_results() {

	global $wie_import_results;

	?>

	<h3 class="title"><?php echo esc_html_x( 'Import Results', 'heading', 'widget-importer-exporter' ); ?></h3>

	<p>
		<?php
		printf(
			wp_kses(
				/* translators: %1$s is URL for widgets screen, %2$s is URL to go back */
				__( 'You can manage your <a href="%1$s">Widgets</a> or <a href="%2$s">Go Back</a>.', 'widget-importer-exporter' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			),
			esc_url( admin_url( 'widgets.php' ) ),
			esc_url( admin_url( basename( $_SERVER['PHP_SELF'] ) . '?page=' . $_GET['page'] ) )
		);
		?>
	</p>

	<table id="wie-import-results">

		<?php
		// Loop sidebars.
		$results = $wie_import_results;
		foreach ( $results as $sidebar ) :
		?>

			<tr class="wie-import-results-sidebar">
				<td colspan="2" class="wie-import-results-sidebar-name">
					<?php
					// Sidebar name if theme supports it; otherwise ID.
					echo esc_html( $sidebar['name'] );
					?>
				</td>
				<td class="wie-import-results-sidebar-message wie-import-results-message wie-import-results-message-<?php echo esc_attr( $sidebar['message_type'] ); ?>">
					<?php
					// Sidebar may not exist in theme.
					echo esc_html( $sidebar['message'] );
					?>
				</td>
			</tr>

			<?php
			// Loop widgets.
			foreach ( $sidebar['widgets'] as $widget ) :
			?>

				<tr class="wie-import-results-widget">
					<td class="wie-import-results-widget-name">
						<?php
						// Widget name or ID if name not available (not supported by site).
						echo esc_html( $widget['name'] );
						?>
					</td>
					<td class="wie-import-results-widget-title">
						<?php
						// Shows "No Title" if widget instance is untitled.
						echo esc_html( $widget['title'] );
						?>
					</td>
					<td class="wie-import-results-widget-message wie-import-results-message wie-import-results-message-<?php echo esc_attr( $widget['message_type'] ); ?>">
						<?php
						// Sidebar may not exist in theme.
						echo esc_html( $widget['message'] );
						?>
					</td>
				</tr>

			<?php endforeach; ?>

			<tr class="wie-import-results-space">
				<td colspan="100%"></td>
			</tr>

		<?php endforeach; ?>

	</table>
	<?php
}
