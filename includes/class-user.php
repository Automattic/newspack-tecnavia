<?php
/**
 * User class.
 *
 * @package Newspack\TecnaviaIntegration
 */

namespace Newspack\TecnaviaIntegration;

/**
 * Class to handle token management for the Tecnavia Integration.
 *
 * @since 1.0
 */
class User {

	/**
	 * The user token meta field.
	 */
	const TECNAVIA_TOKEN_META_FIELD = 'np_wc_tecnavia_token';

	/**
	 * Token expiration time in seconds.
	 */
	const TOKEN_EXPIRATION_TIME = YEAR_IN_SECONDS;

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		// Setup Hooks & Filters.
		add_action( 'wp_login', array( __CLASS__, 'add_or_create_user_token' ), 10, 2 );
	}

	/**
	 * Adds or creates a user token.
	 *
	 * @param string   $user_login The user's login.
	 * @param \WP_User $user The user object.
	 */
	public static function add_or_create_user_token( $user_login, $user ) {
		// Get the user token.
		$token = get_user_meta( $user->ID, self::TECNAVIA_TOKEN_META_FIELD, true );

		// If the user token is empty or expired, create a new one.
		$token = self::maybe_refresh_token( $token, $user->ID );
	}

	/**
	 * Creates a user token.
	 *
	 * @param int $user_id The user ID.
	 * @return string
	 */
	public static function create_user_token( $user_id ) {
		// Generate a token.
		$token = wp_generate_password( 32, false );

		// Save the token.
		update_user_meta( $user_id, self::TECNAVIA_TOKEN_META_FIELD, $token );

		// Save the token expiration time.
		update_user_meta( $user_id, self::TECNAVIA_TOKEN_META_FIELD . '_expiration_time', time() + self::TOKEN_EXPIRATION_TIME );

		// Return the token.
		return $token;
	}

	/**
	 * Gets the user token.
	 *
	 * @param int $user_id The user ID.
	 * @return string
	 */
	public static function get_user_token( $user_id ) {
		$token = get_user_meta( $user_id, self::TECNAVIA_TOKEN_META_FIELD, true );

		$token = self::maybe_refresh_token( $token, $user_id );

		return $token;
	}

	/**
	 * Maybe refreshes the user token.
	 *
	 * @param string $token The user token.
	 * @param int    $user_id The user ID.
	 * @return string
	 */
	public static function maybe_refresh_token( $token, $user_id ) {
		if ( empty( $token ) || self::is_user_token_expired( $user_id ) ) {
			$token = self::create_user_token( $user_id );
		}

		return $token;
	}

	/**
	 * Checks if the user token is expired.
	 *
	 * @param int $user_id The user ID.
	 * @return bool
	 */
	public static function is_user_token_expired( $user_id ) {
		// Get the token expiration time.
		$token_expiration_time = get_user_meta( $user_id, self::TECNAVIA_TOKEN_META_FIELD . '_expiration_time', true );

		return $token_expiration_time < time();
	}

	/**
	 * Gets the user Tecnavia URL.
	 *
	 * @param int $user_id The user ID.
	 * @return string
	 */
	public static function get_user_tecnavia_url( $user_id ) {
		// Get the user token.
		$token = self::get_user_token( $user_id );

		// Get the Tecnavia URL.
		$tecnavia_url = Settings::get_tecnavia_url();

		// Check if the Tecnavia URL is empty.
		if ( empty( $tecnavia_url ) || empty( $token ) ) {
			return '';
		}

		// Generate user's e-edition URL.
		$user_tecnavia_url = add_query_arg( sanitize_key( 'token' ), sanitize_text_field( $token ), $tecnavia_url );

		return $user_tecnavia_url;
	}

	/**
	 * Checks if the user has access to Tecnavia.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return bool
	 */
	public static function check_user_tecnavia_access( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		// Get the tecnavia access permissions.
		$tecnavia_access_permissions = Settings::get_tecnavia_access_permissions();

		/**
		 * 1. Check if all registered users have access.
		 */
		$all_registered_users_have_access = 'yes' === $tecnavia_access_permissions[ Settings::ALL_REGISTERED_USERS_ACCESS_KEY ] ? true : false;

		// Since all registered users have access, return true.
		if ( $all_registered_users_have_access ) {
			return true;
		}

		/**
		 * 2. Check if the user's role has access.
		 */
		$allowed_roles = $tecnavia_access_permissions[ Settings::ALLOWED_ROLES_ACCESS_KEY ];
		$user_object   = get_userdata( $user_id );
		$user_roles    = $user_object->roles;

		// Check if the user has access based on their role.
		if ( ! empty( $user_roles ) && ! empty( $allowed_roles ) ) {
			foreach ( $user_roles as $user_role ) {
				if ( in_array( $user_role, $allowed_roles, true ) ) {
					return true;
				}
			}
		}

		/**
		 * 3. Check if the user has access based on their subscription.
		 * Note: This check is only applicable if the WooCommerce Subscriptions plugin is active.
		 */
		if ( class_exists( 'WC_Subscriptions' ) ) {
			// Get the allowed subscription product IDs.
			$allowed_subscriptions_product_ids = array_map( 'intval', $tecnavia_access_permissions[ Settings::ALLOWED_SUBSCRIPTIONS_ACCESS_KEY ] );

			// Check if the user has active subscription to any of the allowed subscription product.
			if ( ! empty( $allowed_subscriptions_product_ids ) ) {
				foreach ( $allowed_subscriptions_product_ids as $product_id ) {
					if ( wcs_user_has_subscription( $user_id, $product_id, 'active' ) ) {
						return true;
					}
				}
			}
		}

		/**
		 * 4. Check if the user has access based on their membership.
		 * Note: This check is only applicable if the WooCommerce Memberships plugin is active.
		 */
		if ( class_exists( 'WC_Memberships' ) ) {
			// Get the allowed membership plan IDs.
			$allowed_membership_ids = array_map( 'intval', $tecnavia_access_permissions[ Settings::ALLOWED_MEMBERSHIPS_ACCESS_KEY ] );
			$user_memberships       = wc_memberships_get_user_active_memberships( $user_id );

			// Check if the user has active membership to any of the allowed memberships.
			if ( ! empty( $user_memberships ) && ! empty( $allowed_membership_ids ) ) {
				foreach ( $user_memberships as $membership ) {
					if ( in_array( $membership->plan_id, $allowed_membership_ids, true ) ) {
						return true;
					}
				}
			}
		}

		// If none of the checks passed, return false.
		return false;
	}
}
