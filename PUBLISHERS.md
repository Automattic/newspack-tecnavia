# Newspack-Tecnavia Plugin Setup Guide

The Newspack-Tecnavia plugin allows publishers to integrate Tecnavia's e-Edition services into their WordPress site, providing readers with access to digital editions directly from their WordPress website based on authentication configurations.

## Prerequisites
This Plugin has the following plugins as dependencies:-

1. Newspack.
2. Woocommerce.
3. Woocommerce Subscriptions.
4. Woocommerce Memberships.

Please install, activate and configure the above plugins before activating and configuing this plugin.

## Installation
1. **Download the Plugin**:
	 - Obtain the latest version of the plugin from the [GitHub repository](https://github.com/Automattic/newspack-tecnavia).

2. **Upload to WordPress**:
	- Navigate to `Plugins` > `Add New` > `Upload Plugin`.
	- Select the downloaded plugin file and click `Install Now`.

3. **Activate the Plugin**:
   - Click `Activate` to enable the plugin.
	- Check if there is a notice to install the required plugins.

## Configuration
1. **Access Plugin Settings**:
   - In the WordPress admin dashboard, navigate to `Woocommerce` > `Settings` > `Newspack Tecnavia Integration`.

   ![Plugin Settings](https://github.com/user-attachments/assets/6705be15-8f7b-4e2d-b7a3-0a140691186b)

2. **Add Tecnavia Configurations**:
   - **E-edition URL Endpoint**: This will be the endpoint on your site which will lead users to the e-Edition.
   For example:- an endpoint could be `e-edition`. This would mean that the e-Edition URL would be `https://your-site.com/e-edition`.
   - **E-edition Endpoint Link Label**: This will be the text of the link that users will click on to access the e-Edition. This link will be displayed on the users `my-account` page.
   - **Tecnavia URL**: This is the URL of your Tecnavia e-Edition. The user will be redirected to this URL when they click on the e-Edition link.
   - **Fallback Page**: Select the page where users will be redirected if they do not have access to the e-Edition. You can use this page to funnel users into e-edition. For instance, it contain information on which memberships/subscriptions could get you an e-edition access.

   ![Integration Settings](https://github.com/user-attachments/assets/2126ecc2-2a4c-4c48-a590-160c9ce4d61e)

3. **Configure User Access**: 
   - The Plugin allows you to restrict access to the e-Edition based on your users role, memberships or subscriptions.
   - Following are the options to configure user's access to the e-Edition:
     - **All registered users**: If this option is selected, all registered users will have access to the e-Edition. Regardless of their role, membership or subscription.
	 - **User Role**: Select the user role that will have access to the e-Edition.
	 - **Membership**: Select the memberships that will grant access to the e-Edition. If a user has any of the selected memberships, the user will be granted access to the e-Edition.
	 - **Subscription**: Select the subscription products that will grant access to the e-Edition. If a user has any of the selected subscription product, the user will be granted access to the e-Edition.

	- Note: The above options are inclusive, meaning that if a user has a role, membership or subscription that is selected, they will have access to the e-Edition. The user does not need to have all of the configured options to access the e-Edition.

   ![User Access Settings](https://github.com/user-attachments/assets/02131d96-e58b-455f-9356-e8c2ef7d0656)

4. **Save Changes**:
   - Click `Save changes` to apply the configurations.

## Usage
   - Once the plugin is configured, your users will see a link for an `e-Edition` on their `my-account` page.
   - The link's text will be the `E-edition Endpoint Link Label` configured in the plugin settings.
   - Clicking on the link will redirect the user to the Tecnavia e-edition if they have the required access.

![User Access Link](https://github.com/user-attachments/assets/3c10afee-dc24-4a83-8f08-7d674c0da702)

## Support
- **Tecnavia Support**: Contact Tecnavia for issues related to your e-Edition content or API access.
- **Newspack Support**: Reach out to the Newspack team for plugin-related inquiries.
