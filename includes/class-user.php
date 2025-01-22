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
		if ( empty( $token ) || self::is_user_token_expired( $user->ID ) ) {
			$token = self::create_user_token( $user->ID );
		}
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
		return get_user_meta( $user_id, self::TECNAVIA_TOKEN_META_FIELD, true );
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
		// Perform checks to see if the user has access to Tecnavia.
		// @TODO: Implement this method.

		return true;
	}
}
