<?php
/**
 * Redirection class.
 *
 * @package Newspack\TecnaviaIntegration
 */

namespace Newspack\TecnaviaIntegration;

/**
 * Class to handle the redirections for the Tecnavia Integration.
 *
 * @since 1.0
 */
class Redirection {

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		// Setup Hooks & Filters.
		add_action( 'init', array( __CLASS__, 'add_tecnavia_redirect_rewrite_rule' ) );
		add_action( 'query_vars', array( __CLASS__, 'add_tecnavia_redirect_query_vars' ) );
		add_action( 'template_include', array( __CLASS__, 'redirect_to_tecnavia' ) );
		add_filter( 'allowed_redirect_hosts', array( __CLASS__, 'add_tecnavia_redirect_host' ) );
	}

	/**
	 * Adds the e-edition rewrite rule.
	 */
	public static function add_tecnavia_redirect_rewrite_rule() {
		// Get the e-edition endpoint.
		$e_edition_endpoint = sanitize_text_field( Settings::get_e_edition_url_endpoint() );

		// If the e-edition endpoint is empty, return.
		if ( empty( $e_edition_endpoint ) ) {
			return;
		}

		// Add the rewrite rule.
		add_rewrite_rule( $e_edition_endpoint . '/?$', 'index.php?tecnavia_redirect=true', 'top' );
	}

	/**
	 * Adds the query vars.
	 *
	 * @param array $vars The query vars.
	 * @return array
	 */
	public static function add_tecnavia_redirect_query_vars( $vars ) {
		$vars[] = 'tecnavia_redirect';
		return $vars;
	}

	/**
	 * Redirects to Tecnavia.
	 *
	 * @param string $template The template.
	 * @return string
	 */
	public static function redirect_to_tecnavia( $template ) {
		// If the tecnavia_redirect query var is not set, return.
		if ( ! get_query_var( 'tecnavia_redirect' ) ) {
			return $template;
		}

		// Check if the user is logged in and has permission to access Tecnavia.
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( wp_login_url( get_permalink() ) );
			exit;
		} elseif ( ! User::check_user_tecnavia_access( get_current_user_id() ) ) {
			// Get fallback page ID.
			$fallback_page_id = Settings::get_fallback_page_id();

			// Check if fallback page is set.
			if ( empty( $fallback_page_id ) ) {
				wp_safe_redirect( home_url() );
				exit;
			}

			// Redirect to the fallback page.
			wp_safe_redirect( get_permalink( $fallback_page_id ), 301 );
			exit;
		}

		// Get the user's Tecnavia URL.
		$tecnavia_url = User::get_user_tecnavia_url( get_current_user_id() );

		// If the Tecnavia URL is empty, return.
		if ( empty( $tecnavia_url ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}

		// Redirect to Tecnavia.
		wp_safe_redirect( $tecnavia_url, 301 );
		exit;
	}

	/**
	 * Adds the Tecnavia redirect host.
	 *
	 * @param array $hosts The allowed hosts.
	 * @return array
	 */
	public static function add_tecnavia_redirect_host( $hosts ) {
		// Add the Tecnavia URL host to the allowed hosts.
		$hosts[] = wp_parse_url( Settings::get_tecnavia_url(), PHP_URL_HOST );

		return $hosts;
	}
}
