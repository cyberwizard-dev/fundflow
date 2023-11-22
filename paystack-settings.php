<?php

// Function to register settings
function paystack_register_settings()
{
    register_setting('paystack_settings_group', 'paystack_public_key');
    register_setting('paystack_settings_group', 'paystack_thank_you_message');
    register_setting('paystack_settings_group', 'paystack_min_donation');
    register_setting('paystack_settings_group', 'paystack_currency');
    register_setting('paystack_settings_group', 'paystack_max_donation');
}

add_action('admin_init', 'paystack_register_settings');

// Function to add menu page
function paystack_menu()
{
    add_menu_page('Paystack Settings', 'Paystack Settings', 'manage_options', 'paystack-donation-settings', 'paystack_settings_page', 'dashicons-admin-generic');
}

add_action('admin_menu', 'paystack_menu');

// Function to display settings page
function paystack_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Paystack Donation Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('paystack_settings_group'); ?>
            <?php do_settings_sections('paystack-donation-settings'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Paystack Public Key</th>
                    <td>
                        <input type="text" name="paystack_public_key"
                               value="<?php echo esc_attr(get_option('paystack_public_key')); ?>"/>
                        <p class="description">Enter your Paystack public key.</p>
                    </td>
                </tr>

                <tr valign='top'>
                    <th scope='row'>CURRENCY [DEFAULT :NGN] </th>
                    <td>
                        <input type='number' name='paystack_currency'
                               value="<?php echo esc_attr(get_option('paystack_currency')) ?? 'NGN'; ?>"/>
                        <p class='description'>[OPTIONS: GHS,NGN,USD,ZAR,KES ]</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Thank You Message</th>
                    <td>
                        <textarea
                                name="paystack_thank_you_message"><?php echo esc_attr(get_option('paystack_thank_you_message')); ?></textarea>
                        <p class="description">Enter the thank you message to be displayed after a successful
                            donation.</p>
                    </td>
                </tr>





                <tr valign="top">
                    <th scope="row">Minimum Donation Amount</th>
                    <td>
                        <input type="number" name="paystack_min_donation"
                               value="<?php echo esc_attr(get_option('paystack_min_donation')); ?>"/>
                        <p class="description">Enter the minimum donation amount.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Maximum Donation Amount</th>
                    <td>
                        <input type="number" name="paystack_max_donation"
                               value="<?php echo esc_attr(get_option('paystack_max_donation')); ?>"/>
                        <p class="description">Enter the maximum donation amount.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
