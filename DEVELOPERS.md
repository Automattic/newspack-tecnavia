## Documentation

The Newspack-Tecnavia plugin integrates Tecnavia's e-Edition services into the Newspack platform, enabling publishers to offer digital editions of their publications seamlessly within their WordPress sites.

## Architecture

The plugin is structured to ensure a clear separation between its core functionalities and the WordPress environment. Key components include:

- **Plugin File (`newspack-tecnavia-integration.php`)**: Defines necessary constants, loads dependencies, and initializes the plugin.
- **Includes Directory (`/includes`)**: Contains PHP classes that handle the core logic of the plugin. Code execution flow begins from `class-initializer.php`.
- **JavaScript Directory (`/src/js`)**: Contains JavaScript/CSS files responsible for client-side interactions and enhancements.

## Token Generation

To securely access Tecnavia's e-edition services, the plugin generates and stores tokens. The process involves:

1. **Token Generation**: When a user logs in, the plugin generates(if not already present) a random token and stores it as a user meta field.
2. **Token Expiry**: Tokens are set to expire after a certain period. The plugin checks the token's expiry date and generates a new one if necessary.

File: `includes/class-user.php`

## Configuration

The plugin requires configuration settings to be set up correctly. Key configurations include:
1. Tecnavia URL - URL for e-edition.
2. e-edition endpoint & label - Endpoint for access to e-edition and label to display on the site.
3. Fallback URL - URL to redirect users if they do not have access to e-edition services.
4. Access control - Define access control settings for users based on their role, subscription, or membership status.

Files:
- `includes/class-setting.php`
- `includes/woocommerce/class-wc-settings-option-tab.php`

## Authentication

The plugin authenticates users using the generated tokens. The process involves:

1. **Token Verification**: When a user accesses e-Edition services, the user is authorized by checking if the user should have access to the service. If no, the user is redirected to the fallback page. If yes, the user is redirected to the e-Edition service along with the token as a query parameter.

2. **Token Validation**: The Tecnavia server makes a REST API request to the publisher's site with the token. The plugin identifies the user based on the token and validates the request. Based on the validation, the plugin either allows or denies access to the e-Edition service. The response sent back to Tecnavia includes the user's information in XML format as per Tecnavia's requirements.

Files:
- `includes/class-user.php`
- `includes/class-redirection.php`

## REST API

The plugin exposes a REST API endpoint to handle requests from Tecnavia. The endpoint is used to validate user's access to e-Edition services and send user information back to Tecnavia. The endpoint will be used by Tecnavia to verify the user's access and retrieve user information. Details:-

- **Endpoint**: `/wp-json/newspack-tecnavia/v1/validate-token`
- **Method**: `POST`
- **Request Body**: Token (string). 32 characters long.
- **Response**: User information in XML format


Sample Authorization Successful Response:-
```xml
<LOGIN>
	<UNIQUE_USER_ID>Test User</UNIQUE_USER_ID>
	<TOKEN>a1b2c3d4e5f6g7h8i9j0</TOKEN>
	<EMAIL>testuser@gmail.com</EMAIL>
	<USER_NAME>test-user</USER_NAME>
	<IS_LOGGED>Yes</IS_LOGGED>
	<FOD>1111111</FOD>
</LOGIN>
```

Authorization Failure Response:-
```xml
<LOGIN>
	<UNIQUE_USER_ID>Test User</UNIQUE_USER_ID>
	<TOKEN>a1b2c3d4e5f6g7h8i9j0</TOKEN>
	<IS_LOGGED>No</IS_LOGGED>
</LOGIN>
```

File: `includes/class-rest-controller.php`

## Flow

1. User logs in to the site.
2. The plugin generates and stores a token for the user if not already present.
3. User accesses the e-Edition service. The plugin verifies the user's access based on the token.
4. If the user has access, the plugin redirects the user to the e-Edition service with the token.
5. Tecnavia server makes a REST API request to the publisher's site with the token.
6. The plugin validates the token's user and its access to the e-Edition service.
7. The plugin sends the user's information back to Tecnavia in XML format as response.
8. If the user does not have access, the plugin redirects the user to the fallback page.

## Dependencies

The plugin relies on:

- **Newspack Plugin**: Leverages functionalities provided by the Newspack plugin.
- **Woocommerce Plugin**: Utilizes WooCommerce as a dependency.
- **Woocommerce Subscriptions Plugin**: Requires WooCommerce Subscriptions for subscription management.
- **Woocommerce Memberships Plugin**: Requires WooCommerce Memberships for membership management.
- **Tecnavia**: Requires access to Tecnavia for e-Edition services.

## Security Considerations

- **Data Sanitization**: Ensure all data inputs are sanitized to prevent security vulnerabilities.
- **Capability Checks**: Verify user permissions before allowing access to certain functionalities.

---
