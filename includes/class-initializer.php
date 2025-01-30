<?php
/**
 * Newspack Tecnavia Integration Initializer.
 *
 * @package Newspack\TecnaviaIntegration
 */

namespace Newspack\TecnaviaIntegration;

/**
 * Class to handle the plugin initialization
 */
class Initializer {

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		// Setup Hooks & Filters.
		add_action( 'admin_notices', array( __CLASS__, 'show_admin_notice__error' ) );

		Settings::init();
		User::init();
		Redirection::init();
		REST_Controller::init();
	}

	/**
	 * Check and displays plugin specific notices when required.
	 *
	 * @return bool Return false on error.
	 */
	public static function has_valid_dependencies() {
		if ( ! DependencyChecker::is_wc_installed()
			|| ! DependencyChecker::is_wc_memberships_installed()
			|| ! DependencyChecker::is_wc_subscriptions_installed()
		) {
			return false;
		}
		return true;
	}

	/**
	 * Displays admin notice summarizing error.
	 */
	public static function show_admin_notice__error() {
		$plugin_notice    = '';
		$inactive_plugins = array();
		$allowed_html     = array(
			'a'      => array(
				'href' => array(),
			),
			'strong' => array(),
		);

		if ( ! DependencyChecker::is_wc_installed() ) {
			$inactive_plugins[] = 'WooCommerce';
		}

		if ( ! DependencyChecker::is_wc_memberships_installed() ) {
			$inactive_plugins[] = 'WooCommerce Memberships';
		}

		if ( ! DependencyChecker::is_wc_subscriptions_installed() ) {
			$inactive_plugins[] = 'WooCommerce Subscriptions';
		}

		if ( ! empty( $inactive_plugins ) ) {
			// Based on the number of inactive plugins, display the appropriate message.
			if ( 1 === count( $inactive_plugins ) ) {
				$plugin_notice = sprintf(
					/* translators: %s: Plugin name. */
					esc_html__( 'Newspack Tecnavia Integration requires %s to be installed and activated.', 'newspack-tecnavia-integration' ),
					'<strong>' . esc_html( $inactive_plugins[0] ) . '</strong>'
				);
			} else {
				$plugin_notice = sprintf(
					/* translators: %s: Plugin names. */
					esc_html__( 'Newspack Tecnavia Integration requires %s to be installed and activated.', 'newspack-tecnavia-integration' ),
					'<strong>' . esc_html( implode( ', ', $inactive_plugins ) ) . '</strong>'
				);
			}
		}

		if ( ! empty( $plugin_notice ) ) {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					echo wp_kses( $plugin_notice, $allowed_html );
					?>
				</p>
			</div>
			<?php
		}
	}
}
