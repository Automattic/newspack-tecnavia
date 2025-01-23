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
	 * Newspack Tecnavia Integration permissions.
	 *
	 * @var array
	 */
	private $tecnavia_permissions_option;

	/**
	 * All registered users.
	 *
	 * @var string
	 */
	private $np_tecnavia_access_setting_all_registered_users_key;

	/**
	 * Allowed roles.
	 *
	 * @var string
	 */
	private $np_tecnavia_access_setting_allowed_roles_key;

	/**
	 * Allowed subscriptions.
	 *
	 * @var string
	 */
	private $np_tecnavia_access_setting_allowed_subscriptions_key;

	/**
	 * Allowed memberships.
	 *
	 * @var string
	 */
	private $np_tecnavia_access_setting_allowed_memberships_key;

	/**
	 * Version of the Tecnavia assets.
	 *
	 * @var string
	 */
	const NEWSPACK_TECNAVIA_ASSETS_VERSION = '1.0';

	/**
	 * Tab ID.
	 *
	 * @var string
	 */
	const NEWSPACK_TECNAVIA_SETTINGS_TAB = 'newspack-tecnavia-integration-settings';

	/**
	 * Constructor.
	 *
	 * @param string $e_edition_endpoint_url_option Newspack Tecnavia Integration e-edition endpoint URL.
	 * @param string $technavia_url_option Newspack Tecnavia Integration Tecnavia URL.
	 * @param string $e_edition_endpoint_link_label Newspack Tecnavia Integration e-edition endpoint link label.
	 * @param string $fallback_page_id_option Newspack Tecnavia Integration fallback page ID.
	 * @param string $tecnavia_permissions_option Newspack Tecnavia Integration permissions.
	 * @param string $np_tecnavia_access_setting_all_registered_users_key All registered users.
	 * @param string $np_tecnavia_access_setting_allowed_roles_key Allowed roles.
	 * @param string $np_tecnavia_access_setting_allowed_subscriptions_key Allowed subscriptions.
	 * @param string $np_tecnavia_access_setting_allowed_memberships_key Allowed memberships.
	 */
	public function __construct(
		$e_edition_endpoint_url_option,
		$technavia_url_option,
		$e_edition_endpoint_link_label,
		$fallback_page_id_option,
		$tecnavia_permissions_option,
		$np_tecnavia_access_setting_all_registered_users_key,
		$np_tecnavia_access_setting_allowed_roles_key,
		$np_tecnavia_access_setting_allowed_subscriptions_key,
		$np_tecnavia_access_setting_allowed_memberships_key
	) {
		/**
		 * Initialize constants.
		 */
		$this->e_edition_endpoint_url_option = $e_edition_endpoint_url_option;
		$this->e_edition_endpoint_link_label = $e_edition_endpoint_link_label;
		$this->technavia_url_option          = $technavia_url_option;
		$this->fallback_page_id_option       = $fallback_page_id_option;
		$this->tecnavia_permissions_option   = $tecnavia_permissions_option;

		/**
		 * Initialize access settings constants.
		 */
		$this->np_tecnavia_access_setting_all_registered_users_key = $np_tecnavia_access_setting_all_registered_users_key;
		$this->np_tecnavia_access_setting_allowed_roles_key        = $np_tecnavia_access_setting_allowed_roles_key;
		$this->np_tecnavia_access_setting_allowed_subscriptions_key = $np_tecnavia_access_setting_allowed_subscriptions_key;
		$this->np_tecnavia_access_setting_allowed_memberships_key   = $np_tecnavia_access_setting_allowed_memberships_key;

		/**
		 * Define the tab name and label
		 */
		$this->id    = self::NEWSPACK_TECNAVIA_SETTINGS_TAB;
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

		// Add custom javascript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_tab_scripts' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		/**
		 * Define the settings keys.
		 * Store the access settings as an array under a single option ( $tecnavia_permissions_option ).
		 */
		$all_registered_users  = '[' . $this->np_tecnavia_access_setting_all_registered_users_key . ']';
		$allowed_roles         = '[' . $this->np_tecnavia_access_setting_allowed_roles_key . ']';
		$allowed_subscriptions = '[' . $this->np_tecnavia_access_setting_allowed_subscriptions_key . ']';
		$allowed_memberships   = '[' . $this->np_tecnavia_access_setting_allowed_memberships_key . ']';

		/**
		 * Settings for the Tecnavia Integration
		 */
		$settings = array(
			// License input.
			array(
				'title' => __( 'Tecnavia Integration Settings', 'newspack-tecnavia-integration' ),
				'type'  => 'title',
				'desc'  => __( 'Settings for the Tecnavia Integration', 'newspack-tecnavia-integration' ),
				'id'    => 'np_wc_tecnavia_settings',
			),
			array(
				'title' => __( 'E-edition Endpoint URL', 'newspack-tecnavia-integration' ),
				'type'  => 'text',
				'desc'  => __( 'Add the endpoint for the URL of e-edition.', 'newspack-tecnavia-integration' ),
				'id'    => $this->e_edition_endpoint_url_option,
				'css'   => 'min-width:600px;',
			),
			array(
				'title' => __( 'E-edition Endpoint Link Label', 'newspack-tecnavia-integration' ),
				'type'  => 'text',
				'desc'  => __( 'Add the label for the e-edition link.', 'newspack-tecnavia-integration' ),
				'id'    => $this->e_edition_endpoint_link_label,
				'css'   => 'min-width:300px;',
			),
			array(
				'title' => __( 'Tecnavia URL', 'newspack-tecnavia-integration' ),
				'type'  => 'text',
				'desc'  => __( 'Add the URL of the Tecnavia service.', 'newspack-tecnavia-integration' ),
				'id'    => $this->technavia_url_option,
				'css'   => 'min-width:600px;',
			),
			array(
				'title' => __( 'Fallback Page', 'newspack-tecnavia-integration' ),
				'type'  => 'single_select_page',
				'desc'  => __( 'Select the fallback page in case the user does not have access.', 'newspack-tecnavia-integration' ),
				'id'    => $this->fallback_page_id_option,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'np_wc_tecnavia_settings',
			),

			// Settings for the access criteria.
			array(
				'title' => __( 'Access Criteria', 'newspack-tecnavia-integration' ),
				'type'  => 'title',
				'desc'  => __( 'Define the criteria for reader access to Tecnavia.', 'newspack-tecnavia-integration' ),
				'id'    => 'np_wc_tecnavia_access_settings',
			),
			
			// Checkbox: All registered users have access.
			array(
				'title'             => __( 'Allow all Registered Users', 'newspack-tecnavia-integration' ),
				'type'              => 'checkbox',
				'desc'              => __( 'Grant access to all registered users. NOTE: This will overrule other settings.', 'newspack-tecnavia-integration' ),
				'id'                => $this->tecnavia_permissions_option . $all_registered_users,
				'custom_attributes' => array(
					'data-dependency' => true,
				),
			),
		);

		// Add roles settings.
		$settings[] = array(
			'title'             => __( 'Allowed Roles', 'newspack-tecnavia-integration' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select roles that have access.', 'newspack-tecnavia-integration' ),
			'id'                => $this->tecnavia_permissions_option . $allowed_roles,
			'class'             => 'wc-enhanced-select',
			'options'           => $this->get_roles_options(),
			'css'               => 'min-width:600px;',
			'custom_attributes' => array(
				'data-dependent-on' => $this->tecnavia_permissions_option . $all_registered_users,
			),
		);

		// Add WooCommerce Subscriptions settings if the plugin is active.
		if ( class_exists( 'WC_Subscriptions' ) ) {
			$settings[] = array(
				'title'             => __( 'By Woo Subscription', 'newspack-tecnavia-integration' ),
				'type'              => 'multiselect',
				'desc'              => __( 'Select WooCommerce Subscriptions that grant access.', 'newspack-tecnavia-integration' ),
				'id'                => $this->tecnavia_permissions_option . $allowed_subscriptions,
				'class'             => 'wc-enhanced-select',
				'options'           => $this->get_woocommerce_subscription_products_options(),
				'css'               => 'min-width:600px;',
				'custom_attributes' => array(
					'data-dependent-on' => $this->tecnavia_permissions_option . $all_registered_users,
				),
			);
		}

		// Add WooCommerce Memberships settings if the plugin is active.
		if ( class_exists( 'WC_Memberships' ) ) {
			$settings[] = array(
				'title'             => __( 'By Woo Membership', 'newspack-tecnavia-integration' ),
				'type'              => 'multiselect',
				'desc'              => __( 'Select WooCommerce Memberships that grant access.', 'newspack-tecnavia-integration' ),
				'id'                => $this->tecnavia_permissions_option . $allowed_memberships,
				'class'             => 'wc-enhanced-select',
				'options'           => $this->get_woocommerce_memberships_options(),
				'css'               => 'min-width:600px;',
				'custom_attributes' => array(
					'data-dependent-on' => $this->tecnavia_permissions_option . $all_registered_users,
				),
			);
		}

		// Add the section end.
		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'np_wc_tecnavia_access_settings',
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

	/**
	 * Helper function to fetch WordPress roles.
	 *
	 * @return array
	 */
	private function get_roles_options() {
		// Fetch WordPress roles.
		$roles = wp_roles()->roles;

		// Prepare the options array.
		$options = array();
		foreach ( $roles as $key => $role ) {
			$options[ $key ] = $role['name'];
		}

		return $options;
	}

	/**
	 * Helper function to fetch WooCommerce Subscriptions.
	 *
	 * @return array
	 */
	private function get_woocommerce_subscription_products_options() {
		// Check if WooCommerce Subscriptions is active.
		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return [];
		}

		// Fetch WooCommerce Subscriptions products.
		$subscriptions = wc_get_products(
			array(
				array(
					'variable-subscription', 
					'subscription',
				),
				'limit' => -1,
			)
		);

		// Prepare the options array.
		$options = [];
		foreach ( $subscriptions as $subscription ) {
			$options[ $subscription->get_id() ] = $subscription->get_name();
		}

		return $options;
	}

	/**
	 * Helper function to fetch WooCommerce Membership Plans.
	 *
	 * @return array
	 */
	private function get_woocommerce_memberships_options() {
		// Check if WooCommerce Memberships is active.
		if ( ! class_exists( 'WC_Memberships' ) ) {
			return [];
		}

		// Fetch WooCommerce Membership Plans.
		$memberships = wc_memberships_get_membership_plans();

		// Prepare the options array.
		$options = [];
		foreach ( $memberships as $membership ) {
			$options[ $membership->get_id() ] = $membership->get_name();
		}

		return $options;
	}

	/**
	 * Enqueue the tab scripts.
	 *
	 * @param string $hook The current page hook.
	 */
	public static function enqueue_tab_scripts( $hook ) {
		// Get the current tab.
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Get path to src directory.
		$src_dir = plugins_url( 'src/js/access-settings.js', NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_FILE );

		// Enqueue the script only on the Newspack-Tecnavia settings tab.
		if (
			'woocommerce_page_wc-settings' === $hook &&
			! empty( $current_tab )
			&& self::NEWSPACK_TECNAVIA_SETTINGS_TAB === $current_tab
		) {
			wp_enqueue_script(
				'newspack-tecnavia-integration-admin',
				$src_dir,
				[],
				self::NEWSPACK_TECNAVIA_ASSETS_VERSION,
				true
			);
		}
	}
}
