=== FundFlow ===
Contributors:remicyberwizard
Tags: money,donate,donation,payment,paystack
Requires at least: 4.7
Tested up to: 5.4
Stable tag: 1.5
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

# FundFlow Plugin

FundFlow is a WordPress plugin designed to simplify the integration of a donation form into your WordPress site.
Customize settings, receive contributions, and show appreciation with personalized thank-you messages.


## Installation

1. Download the FundFlow plugin from [GitHub](https://github.com/cyberwizard-dev/fundflow).
2. Upload the plugin to your WordPress site.
3. Activate the FundFlow plugin through the WordPress Plugins menu.

## License

This plugin is licensed under the [GNU General Public License v2 (or later)](https://www.gnu.org/licenses/gpl-2.0.html).
You must include a copy of the license in the root of your plugin folder.

## Configuration

### General Settings

1. **Paystack Public Key:** Enter your Paystack public key in the designated field.

2. **Currency [Default: NGN]:** Set the currency for donations. Options include GHS, NGN, USD, ZAR, and KES.

3. **Thank You Message:** Customize the thank-you message displayed after a successful donation.

4. **Minimum Donation Amount:** Set the minimum donation amount (cannot be lower than 100).

5. **Maximum Donation Amount:** Set the maximum donation amount (must be greater than the minimum).

### Shortcode

Use the `[fundflow]` shortcode to embed the donation form on any page or post.

Example:
```[fundflow]```

## Admin Settings

1. Navigate to the WordPress admin dashboard.
2. Click on "FundFlow Settings" in the left sidebar.
3. Adjust the settings according to your preferences.
4. Click the "Save Changes" button.

## Usage

1. Add the `[fundflow]` shortcode to any page or post where you want the donation form to appear.
2. Users can enter the donation amount, their email, and click "Send Donation."
3. The Paystack payment gateway will handle the donation process.
4. After a successful donation, users will see a thank-you message.

## Additional Notes

- Customize the thank-you message in the plugin's settings.
- For any inquiries or support, contact us at [eminibest@gmail.com](mailto:eminibest@gmail.com).