<?php
/**
 * Settings Menu
 *
 * @package Newspack\TecnaviaIntegration
 */

namespace Newspack\TecnaviaIntegration;

use Newspack\TecnaviaIntegration\WooCommerce\Settings\WC_Settings_Option_Tab;

/**
 * Add Settings to configure the integration.
 *
 * @since 1.0
 */
class Settings {

	/**
	 * Option names.
	 */
	const E_EDITION_ENDPOINT_URL_OPTION  = 'np_wc_tecnavia_e_edition_endpoint_url';
	const E_EDITION_ENDPOINT_LINK_LABEL  = 'np_wc_tecnavia_e_edition_endpoint_link_label';
	const TECNAVIA_URL_OPTION            = 'np_wc_tecnavia_url';
	const FALLBACK_PAGE_ID_OPTION        = 'np_wc_tecnavia_fallback_page_id';

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		// Setup Hooks & Filters.
		add_filter( 'woocommerce_get_settings_pages', array( __CLASS__, 'add_wc_settings_page' ) );
		add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'add_wc_account_menu_item' ), 20, 1 );
		add_filter( 'woocommerce_get_endpoint_url', array( __CLASS__, 'add_wc_endpoint_url' ), 10, 4 );
	}

	/**
	 * Add the Newspack-Tecnavia settings page to WooCommerce settings.
	 *
	 * @param array $settings Array of WooCommerce settings pages.
	 * @return array
	 */
	public static function add_wc_settings_page( $settings ) {
		// Add the Newspack-Tecnavia settings page.
		$settings[] = include_once __DIR__ . '/woocommerce/class-wc-settings-option-tab.php';

		// Initialize the settings page.
		new WC_Settings_Option_Tab(
			self::E_EDITION_ENDPOINT_URL_OPTION,
			self::TECNAVIA_URL_OPTION,
			self::E_EDITION_ENDPOINT_LINK_LABEL,
			self::FALLBACK_PAGE_ID_OPTION
		);

		// Return the settings.
		return $settings;
	}

	/**
	 * Add the Newspack-Tecnavia account menu item to WooCommerce account menu.
	 *
	 * @param array $items Array of WooCommerce account menu items.
	 * @return array
	 */
	public static function add_wc_account_menu_item( $items ) {
		// Create the e-edition link.
		$e_edition_link = array(
			'e-edition' => self::get_e_edition_endpoint_link_label(),
		);

		// Add the e-edition link to the account menu at the second last position.
		$items = array_slice( $items, 0, -1, true ) + $e_edition_link + array_slice( $items, -1, null, true );

		return $items;
	}

	/**
	 * Override the e-edition endpoint URL.
	 *
	 * @param string $url URL of the endpoint.
	 * @param string $endpoint Endpoint name.
	 * @param int    $value Value of the endpoint.
	 * @param string $permalink Permalink of the endpoint.
	 * @return string
	 */
	public static function add_wc_endpoint_url( $url, $endpoint, $value, $permalink ) {
		// Check if the endpoint is the e-edition.
		if ( $endpoint !== 'e-edition' ) {
			return $url;
		}

		// Get the e-edition URL.
		$e_edition_url = self::get_e_edition_endpoint_url();

		// Bail if the e-edition URL is empty.
		if ( empty( $e_edition_url ) ) {
			return $url;
		}

		return $e_edition_url;
	}

	/**
	 * Get the e-edition endpoint URL.
	 *
	 * @return string
	 */
	public static function get_e_edition_url_endpoint() {
		return get_option( self::E_EDITION_ENDPOINT_URL_OPTION );
	}

	/**
	 * Get the e-edition endpoint URL.
	 *
	 * @return string
	 */
	public static function get_e_edition_endpoint_url() {
		return site_url( self::get_e_edition_url_endpoint() );
	}

	/**
	 * Get the e-edition endpoint link label.
	 *
	 * @return string
	 */
	public static function get_e_edition_endpoint_link_label() {
		return get_option( self::E_EDITION_ENDPOINT_LINK_LABEL );
	}

	/**
	 * Get the Tecnavia URL.
	 *
	 * @return string
	 */
	public static function get_tecnavia_url() {
		return esc_url_raw( get_option( self::TECNAVIA_URL_OPTION ) );
	}

	/**
	 * Get the fallback page ID.
	 *
	 * @return int
	 */
	public static function get_fallback_page_id() {
		return get_option( self::FALLBACK_PAGE_ID_OPTION );
	}
}
