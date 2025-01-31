<?php
/**
 * Adds REST Endpoints to validate user tokens.
 *
 * @package Newspack\TecnaviaIntegration
 */

namespace Newspack\TecnaviaIntegration;

use WP_User;
use WP_REST_Server;
use WP_REST_Response;
use SimpleXMLElement;

/**
 * Class to handle REST API Endpoints for Tecnavia Integration.
 *
 * @since 1.0
 */
class REST_Controller {

	/**
	 * Plugin route namespace.
	 */
	const NAMESPACE = 'newspack-tecnavia/v1';

	/**
	 * Endpoint constants.
	 */
	const VALIDATE_TOKEN = '/validate-token';

	/**
	 * Set up hooks and filters.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_api_endpoints' ) );
		add_filter( 'rest_pre_serve_request', array( __CLASS__, 'maybe_add_xml_content_type' ), 10, 4 );
	}

	/**
	 * Registers REST Endpoints for Extended Access.
	 */
	public static function register_api_endpoints() {
		register_rest_route(
			self::NAMESPACE,
			self::VALIDATE_TOKEN,
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'validate_token' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Hijacks the REST API response to serve XML response for the validate token endpoint.
	 *
	 * @param bool            $served Whether the request has already been served.
	 * @param mixed           $result The result to send to the client. Usually a WP_REST_Response.
	 * @param WP_REST_Request $request The request used to generate the response.
	 * @param WP_REST_Server  $server Server instance.
	 * @return bool
	 */
	public static function maybe_add_xml_content_type( $served, $result, $request, $server ) {
		// Check if the request is already served.
		if ( $served ) {
			return $served;
		}

		// Check if the request is for the validate token endpoint.
		if ( '/' . self::NAMESPACE . self::VALIDATE_TOKEN !== $request->get_route() ) {
			return $served;
		}

		// Add the XML content type.
		$server->send_header( 'Content-Type', 'text/xml' );

		// Directly output the XML to skip the JSON encoding.
		echo esc_xml( $result->data );

		return true;
	}

	/**
	 * Validates the user token.
	 *
	 * @param WP_REST_Request $request The REST Request.
	 * @return WP_REST_Response
	 */
	public static function validate_token( $request ) {
		// Get the token from the request.
		$token = $request->get_param( 'token' );

		// If the token is empty, return an error.
		if ( empty( $token ) ) {
			return new WP_REST_Response( self::prepare_failure_response_xml()->asXML(), 200 );
		}

		// Get the user ID by comparing the token.
		$user = get_users(
			array(
				'meta_key'   => User::TECNAVIA_TOKEN_META_FIELD,
				'meta_value' => sanitize_text_field( $token ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'fields'     => array( 'ID', 'display_name', 'user_nicename', 'user_email' ),
				'number'     => 1,
			)
		);

		// If the user is not found, return an error.
		if ( empty( $user ) ) {
			return new WP_REST_Response( self::prepare_failure_response_xml()->asXML(), 200 );
		}

		// Get the user object.
		$user = $user[0];

		// Check if the user has required permissions or the token is expired.
		if ( ! User::check_user_tecnavia_access( $user->ID ) || User::is_user_token_expired( $user->ID ) ) {
			// Prepare invalid XML response.
			$failure_response_xml = self::prepare_failure_response_xml();

			// Return the invalid XML response.
			return new WP_REST_Response( $failure_response_xml->asXML(), 200 );
		}

		// Prepare valid response.
		$success_response_xml = self::prepare_success_response_xml( $user, $token );

		// Return the valid XML response.
		return new WP_REST_Response( $success_response_xml->asXML(), 200 );
	}

	/**
	 * Prepare the failure response XML.
	 *
	 * @return SimpleXMLElement
	 */
	public static function prepare_failure_response_xml() {
		// Prepare the failure response XML.
		$failure_response_xml = new SimpleXMLElement( '<LOGIN></LOGIN>' );

		// Add the user details to the XML.
		$failure_response_xml->addChild( 'TOKEN', '' );
		$failure_response_xml->addChild( 'UNIQUE_USER_ID', '' );
		$failure_response_xml->addChild( 'IS_LOGGED', 'No' );

		return $failure_response_xml;
	}

	/**
	 * Prepare the success response XML.
	 *
	 * @param WP_User $user The user object.
	 * @param string  $token The user token.
	 * @return SimpleXMLElement
	 */
	public static function prepare_success_response_xml( $user, $token ) {
		// Prepare the success response XML.
		$success_response_xml = new SimpleXMLElement( '<LOGIN></LOGIN>' );

		// Add the user details to the XML.
		$success_response_xml->addChild( 'TOKEN', $token );
		$success_response_xml->addChild( 'UNIQUE_USER_ID', $user->user_nicename );
		$success_response_xml->addChild( 'EMAIL', $user->user_email );
		$success_response_xml->addChild( 'USER_NAME', $user->display_name );
		$success_response_xml->addChild( 'IS_LOGGED', 'Yes' );
		// FOD is delivery schedule.
		// 1 Symbolizes delivery on that day. 0 symbolizes no delivery on that day. It goes from Sunday to Saturday.
		// For now, we are hardcoding it to 1111111. means delivery every day.
		$success_response_xml->addChild( 'FOD', '1111111' );

		return $success_response_xml;
	}
}
