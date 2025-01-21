<?php
/**
 * Settings Menu
 *
 * @package Newspack\TecnaviaIntegration
 */

namespace Newspack\TecnaviaIntegration\WooCommerce\Settings;

use WC_Admin_Settings;
use WC_Settings_Page;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the setting required to integrate the plugin's option tab into WooCommerce 'Settings' page.
 */
class WC_Settings_Option_Tab extends WC_Settings_Page {

	/**
	 * Newspack Tecnavia Integration e-edition endpoint URL.
	 *
	 * @var string
	 */
	private $e_edition_endpoint_url_option;

	/**
	 * Newspack Tecnavia Integration e-edition endpoint link label.
	 *
	 * @var string
	 */
	private $e_edition_endpoint_link_label;

	/**
	 * Newspack Tecnavia Integration Tecnavia URL.
	 *
	 * @var string
	 */
	private $technavia_url_option;

	/**
	 * Newspack Tecnavia Integration fallback page ID.
	 *
	 * @var string
	 */
	private $fallback_page_id_option;

	/**
	 * Constructor.
	 *
	 * @param string $e_edition_endpoint_url_option e-edition endpoint URL option name.
	 * @param string $technavia_url_option Tecnavia URL option name.
	 * @param string $e_edition_endpoint_link_label e-edition endpoint link label option name.
	 * @param string $fallback_page_id_option Fallback page ID option name.
	 */
	public function __construct(
		$e_edition_endpoint_url_option,
		$technavia_url_option,
		$e_edition_endpoint_link_label,
		$fallback_page_id_option
	) {
		/**
		 * Initialize constants.
		 */
		$this->e_edition_endpoint_url_option = $e_edition_endpoint_url_option;
		$this->e_edition_endpoint_link_label = $e_edition_endpoint_link_label;
		$this->technavia_url_option          = $technavia_url_option;
		$this->fallback_page_id_option       = $fallback_page_id_option;

		/**
		 * Define the tab name and label
		 */
		$this->id    = 'newspack-tecnavia-integration-settings';
		$this->label = __( 'Newspack Tecnavia Integration', 'newspack-tecnavia-integration' );

		/**
		 * Define all hooks instead of inheriting from parent
		 */
		// Add the tab to the tabs array.
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 60 );

		// Add settings.
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'render_page' ) );

		// Process/save the settings.
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save_settings' ) );
	}


	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		/**
		 * Settings for the Tecnavia Integration
		 */
		$settings = array(
			// License input.
			array(
				'title' => __( 'Tecnavia Settings', 'newspack-tecnavia-integration' ),
				'type'  => 'title',
				'desc'  => __( 'Settings for the Tecnavia Integration', 'newspack-tecnavia-integration' ),
				'id'    => 'np_wc_tecnavia_settings',
			),
			array(
				'title'    => __( 'E-edition Endpoint URL', 'newspack-tecnavia-integration' ),
				'type'     => 'text',
				'desc'     => __( 'Add the URL of the e-edition endpoint.', 'newspack-tecnavia-integration' ),
				'desc_tip' => true,
				'id'       => $this->e_edition_endpoint_url_option,
				'css'      => 'min-width:600px;',
			),
			array(
				'title'    => __( 'E-edition Endpoint Link Label', 'newspack-tecnavia-integration' ),
				'type'     => 'text',
				'desc'     => __( 'Add the label for the e-edition endpoint link.', 'newspack-tecnavia-integration' ),
				'desc_tip' => true,
				'id'       => $this->e_edition_endpoint_link_label,
				'css'      => 'min-width:300px;',
			),
			array(
				'title'    => __( 'Tecnavia URL', 'newspack-tecnavia-integration' ),
				'type'     => 'text',
				'desc'     => __( 'Add the URL of the Tecnavia service.', 'newspack-tecnavia-integration' ),
				'desc_tip' => true,
				'id'       => $this->technavia_url_option,
				'css'      => 'min-width:600px;',
			),
			array(
				'title'    => __( 'Fallback Page', 'newspack-tecnavia-integration' ),
				'type'     => 'single_select_page',
				'desc'     => __( 'Select the fallback page.', 'newspack-tecnavia-integration' ),
				'desc_tip' => true,
				'id'       => $this->fallback_page_id_option,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'np_wc_tecnavia_settings',
			),
		);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output the settings
	 */
	public function render_page() {
		// Fetch the settings.
		$settings = $this->get_settings();

		// Render the settings.
		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save the settings
	 */
	public function save_settings() {
		// Fetch the settings.
		$settings = $this->get_settings();

		// Save the settings.
		WC_Admin_Settings::save_fields( $settings );

		// Trigger an action hook.
		do_action( 'woocommerce_update_options_' . $this->id );
	}
}
