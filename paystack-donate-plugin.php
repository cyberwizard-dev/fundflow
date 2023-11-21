<?php
/*
Plugin Name: Paystack Donation
Description: Add Paystack donation form to anywhere on your WordPress site.
Version: 1.0
Author: Cyberwizard
*/

global $thank_you_message;
// Function to register settings
function paystack_register_settings()
{
    register_setting('paystack_settings_group', 'paystack_public_key');
    register_setting('paystack_settings_group', 'paystack_thank_you_message');
    register_setting('paystack_settings_group', 'paystack_min_donation', 'sanitize_min_donation');
    register_setting('paystack_settings_group', 'paystack_max_donation', 'sanitize_max_donation');
}

function sanitize_min_donation($input)
{
    return max(100, intval($input)); // Ensure minimum is 100
}

function sanitize_max_donation($input)
{
    $min_donation = get_option('paystack_min_donation');
    return max($min_donation + 1, intval($input)); // Ensure maximum is greater than the minimum
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

        <?php
        if (isset($_GET['settings-updated'])) {
            if ($_GET['settings-updated'] === 'true') {
                echo '<div id="message" class="updated notice is-dismissible"><p>Settings updated successfully!</p></div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible"><p>Error updating settings. Please try again.</p></div>';
            }
        }
        ?>

        <form id="paystack-settings-form" method="post" action="options.php">
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
                        <p class="description">Enter the minimum donation amount (cannot be lower than 100).</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Maximum Donation Amount</th>
                    <td>
                        <input type="number" name="paystack_max_donation"
                               value="<?php echo esc_attr(get_option('paystack_max_donation')); ?>"/>
                        <p class="description">Enter the maximum donation amount (must be greater than the minimum).</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var settingsForm = document.getElementById('paystack-settings-form');

            settingsForm.addEventListener('submit', function (e) {
                var minDonation = document.getElementsByName('paystack_min_donation')[0].value;
                var maxDonation = document.getElementsByName('paystack_max_donation')[0].value;

                if (minDonation < 100) {
                    alert('Minimum donation amount must be 100 or more.');
                    e.preventDefault();
                }

                if (maxDonation <= minDonation) {
                    alert('Maximum donation amount must be greater than the minimum.');
                    e.preventDefault();
                }
            });
        });
    </script>

    <?php
}

// Enqueue Paystack script
function paystack_enqueue_scripts(): void
{
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css');
    wp_enqueue_script('paystack-script', 'https://js.paystack.co/v1/inline.js', array('jquery'), null, true);

    $public_key = get_option('paystack_public_key', 'pk_test_fc018c5f85c2c87a539d3f33cef343fc05089170');
    $thank_you_message = get_option('paystack_thank_you_message', '<h4 class=\'text-success mb-3\'>Thank You for Your Donation!</h4><p>Your generosity is greatly appreciated. With your support, we can continue making a positive impact.</p>');

    wp_localize_script('paystack-script', 'paystack_vars', array(
        'public_key' => $public_key,
        'thank_you_message' => $thank_you_message,
    ));
}

add_action('wp_enqueue_scripts', 'paystack_enqueue_scripts');

// Function to add shortcode
function paystack_donation_form_shortcode(): false|string
{
    ob_start();
    ?>
    <div id="paystack-donation-form" class='container mt-5'>
        <div class='card p-3 shadow' style='max-width: 400px; margin: 0 auto;'>
            <form id='paystack-donation-form' class='needs-validation' novalidate>
                <div class='form-group'>
                    <label for='amount'>
                        Amount (NGN)
                    </label>
                    <div class='input-group'>
                        <div class='input-group-prepend'>
                            <span class='input-group-text'>&#x20A6;</span>
                        </div>
                        <input placeholder="5000" type='number' class='form-control' name='amount' id='amount' required>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='email'>
                        Email
                    </label>
                    <div class='input-group'>
                        <div class='input-group-prepend'>
                            <span class='input-group-text'><i class='fas fa-envelope'></i></span>
                        </div>
                        <input placeholder="example@email.com" type='email' class='form-control' name='email'
                               id='email'>
                    </div>
                </div>

                <button type='submit' class='btn btn-success btn-block' id='paystack-donate-button'>Donate with
                    Paystack
                </button>

                <div class='invalid-feedback'>
                    Please enter a valid amount.
                </div>
            </form>
        </div>
    </div>

    <div id='thank-you-card' class='card p-3 shadow' style='max-width: 400px; margin: 0 auto; display: none;'>
        <?php echo  esc_attr(get_option('paystack_public_key')) ?? "We are grateful."?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var paystackForm = document.getElementById('paystack-donation-form');
            var thankYouCard = document.getElementById('thank-you-card');

            paystackForm.addEventListener('submit', function (e) {
                if (!paystackForm.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                paystackForm.classList.add('was-validated');
            });

            var paystackButton = document.getElementById('paystack-donate-button');

            paystackButton.addEventListener('click', function (e) {
                e.preventDefault();

                var amount = document.getElementById('amount').value;
                var email = document.getElementById('email').value;

                // Initialize Paystack
                var handler = PaystackPop.setup({
                    key: paystack_vars.public_key,
                    email: email,
                    amount: amount * 100,
                    ref: 'donation_' + Math.floor((Math.random() * 1000000000) + 1),
                    currency: 'NGN',
                    callback: function (response) {
                        // Handle successful payment
                        console.log(response);
                        hideFormShowThankYou();
                    },
                    onClose: function () {
                        // Handle close
                        console.log('Payment modal closed');
                    }
                });

                // Open Paystack dialog
                handler.openIframe();
            });

            function hideFormShowThankYou() {
                // Hide the donation form
                paystackForm.style.display = 'none';
                // Show the thank you card
                thankYouCard.style.display = 'block';
            }
        });
    </script>

    <?php
    return ob_get_clean();
}

// Register the shortcode for donation form
add_shortcode('paystack_donation_form', 'paystack_donation_form_shortcode');
