<?php
/**
 * DependencyChecker class file.
 * specific to Newspack functionality.
 *
 * @package Newspack\TecnaviaIntegration
 */

namespace Newspack\TecnaviaIntegration;

/**
 * Provide functionality to check the plugin's dependencies on other plugin.
 *
 * @since 1.0
 */
class DependencyChecker {

	const WOOCOMMERCE_PLUGIN_CLASS              = 'WooCommerce';
	const WOOCOMMERCE_MEMBERSHIPS_PLUGIN_CLASS  = 'WC_Memberships';
	const WOOCOMMERCE_SUBSCRIPTION_PLUGIN_CLASS = 'WC_Subscriptions';

	/**
	 * Check if plugin is installed & active by getting all plugins from the plugins dir.
	 *
	 * @param  string $plugin_class Class name of the plugin to check for.
	 * @return bool
	 */
	protected static function check_plugin_installed_and_active( $plugin_class ): bool {
		if ( class_exists( $plugin_class ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check whether 'WooCommerce Memberships' plugin is installed.
	 *
	 * @return bool Return true if plugin installed.
	 */
	public static function is_wc_installed(): bool {
		if ( self::check_plugin_installed_and_active( self::WOOCOMMERCE_PLUGIN_CLASS ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check whether 'WooCommerce Memberships' plugin is installed.
	 *
	 * @return bool Return true if plugin installed.
	 */
	public static function is_wc_memberships_installed(): bool {
		if ( self::check_plugin_installed_and_active( self::WOOCOMMERCE_MEMBERSHIPS_PLUGIN_CLASS ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check whether 'WooCommerce Subscriptions' plugin is installed.
	 *
	 * @return bool Return true if plugin installed.
	 */
	public static function is_wc_subscriptions_installed(): bool {
		if ( self::check_plugin_installed_and_active( self::WOOCOMMERCE_SUBSCRIPTION_PLUGIN_CLASS ) ) {
			return true;
		}
		return false;
	}
}
