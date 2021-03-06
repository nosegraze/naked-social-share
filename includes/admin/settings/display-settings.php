<?php
/**
 * Display the admin options page
 *
 * @package   naked-social-share
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0.0
 * @return void
 */
function nss_options_page() {

	$settings_tabs = nss_get_settings_tabs();
	$settings_tabs = empty( $settings_tabs ) ? array() : $settings_tabs;
	$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $settings_tabs ) ? $_GET['tab'] : 'general';
	$sections      = nss_get_settings_tab_sections( $active_tab );
	$key           = 'main';

	if ( is_array( $sections ) ) {
		$key = key( $sections );
	}

	$registered_sections = nss_get_settings_tab_sections( $active_tab );
	$section             = isset( $_GET['section'] ) && ! empty( $registered_sections ) && array_key_exists( $_GET['section'], $registered_sections ) ? $_GET['section'] : $key;
	ob_start();
	?>
	<div id="nss-settings-wrap" class="wrap">
		<?php if ( count( $settings_tabs ) > 1 ) : ?>
			<h1 class="nav-tab-wrapper">
				<?php
				foreach ( $settings_tabs as $tab_id => $tab_name ) {
					$tab_url = add_query_arg( array(
						'settings-updated'  => false,
						'tab'               => $tab_id,
						'defaults-restored' => false
					) );

					// Remove the section from the tabs so we always end up at the main section
					$tab_url = remove_query_arg( 'section', $tab_url );

					// Add query arg to first section if there's only one and it's not 'main'.
					// This is particularly needed for the add-ons tab where each add-on is given
					// its own section.
					$this_tabs_sections = nss_get_settings_tab_sections( $tab_id );
					if ( is_array( $this_tabs_sections ) && count( $this_tabs_sections ) == 1 && ! array_key_exists( 'main', $this_tabs_sections ) ) {
						$section_keys = array_keys( $this_tabs_sections );
						$tab_url      = add_query_arg( array(
							'section' => $section_keys[0]
						), $tab_url );
					}

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';
					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
					echo '</a>';
				}
				?>
			</h1>
		<?php else : ?>
			<h1><?php _e( 'Naked Social Share Settings', 'naked-social-share' ); ?></h1>
		<?php endif; ?>

		<?php
		$number_of_sections = count( $sections );
		$number             = 0;

		if ( $number_of_sections > 1 ) {
			echo '<div><ul class="subsubsub">';
			foreach ( $sections as $section_id => $section_name ) {
				echo '<li>';
				$number ++;
				$tab_url = add_query_arg( array(
					'settings-updated'  => false,
					'tab'               => $active_tab,
					'section'           => $section_id,
					'defaults-restored' => false
				) );
				$class   = '';
				if ( $section == $section_id ) {
					$class = 'current';
				}
				echo '<a class="' . $class . '" href="' . esc_url( $tab_url ) . '">' . $section_name . '</a>';
				if ( $number != $number_of_sections ) {
					echo ' | ';
				}
				echo '</li>';
			}
			echo '</ul></div>';
		}
		?>

		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
					<?php
					settings_fields( 'naked_social_share_settings' );
					if ( 'main' === $section ) {
						do_action( 'naked-social-share/settings/tab/top', $active_tab );
					}
					do_action( 'naked-social-share/settings/tab/top/' . $active_tab . '_' . $section );
					do_settings_sections( 'naked_social_share_settings_' . $active_tab . '_' . $section );
					do_action( 'naked-social-share/settings/tab/bottom/' . $active_tab . '_' . $section );
					?>
				</table>

				<div class="nss-settings-buttons">
					<?php submit_button(); ?>

					<p id="nss-reset-tab">
						<button type="button" id="nss-reset-settings" name="nss-reset-defaults" class="button-secondary" data-current-tab="<?php echo esc_attr( $active_tab ); ?>" data-current-section="<?php echo esc_attr( $section ); ?>"><?php esc_attr_e( 'Reset All', 'naked-social-share' ); ?></button>
					</p>

					<p id="nss-reset-progress"></p>
				</div>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}