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
		$plugin_notice = '';
		$allowed_html  = array(
			'a' => array(
				'href' => array(),
			),
			'b' => array(),
		);

		if ( ! DependencyChecker::is_wc_installed() ) {
			$plugin_notice = '<b>Newspack Tecnavia Integration</b> plugin requires <b>WooCommerce</b> to be installed, active and configured.';
		} elseif ( ! DependencyChecker::is_wc_memberships_installed() ) {
			$plugin_notice = '<b>Newspack Tecnavia Integration</b> plugin requires <b>WooCommerce Memberships</b> to be installed, active and configured.';
		} elseif ( ! DependencyChecker::is_wc_subscriptions_installed() ) {
			$plugin_notice = '<b>Newspack Tecnavia Integration</b> plugin requires <b>WooCommerce Subscriptions</b> to be installed, active and configured.';
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
