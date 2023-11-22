
<html>
<style>
    span.text-gray-500 {
        margin: -15px;
    }
</style>
</html>
<?php
/*
Plugin Name: FundFlow
Description: Add Donation form to anywhere on your WordPress site.
Version: 1.0
Author: Cyberwizard
Author URI:https://github.com/cyberwizard-dev
*/




// Function to register settings
function fundflow_register_settings(): void
{
    register_setting('paystack_settings_group', 'paystack_public_key');
    register_setting('paystack_settings_group', 'paystack_thank_you_message');
    register_setting('paystack_settings_group', 'paystack_currency');
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

add_action('admin_init', 'fundflow_register_settings');

// Function to add menu page
function paystack_menu(): void
{
    add_menu_page('FundFlow Settings', 'FundFlow Settings', 'manage_options', 'fundflow-donation-settings', 'fundflow_settings_page', 'dashicons-admin-generic');
}

add_action('admin_menu', 'paystack_menu');

// Function to display settings page
function fundflow_settings_page(): void
{
    ?>
    <div class="wrap">
        <h1>FundFlow Settings</h1>

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
                <tr valign='top'>
                    <th scope='row'>CURRENCY [DEFAULT :NGN]</th>
                    <td>
                        <input required type='text' name='paystack_currency'
                               value="<?php if (!empty(get_option('paystack_currency'))) {
                                   echo esc_attr(get_option('paystack_currency'));
                               }else{
                                   echo "NGN";
                                   }?>"/>
                        <p class='description'>[OPTIONS: GHS,NGN,USD,ZAR,KES ]</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Thank You Message</th>
                    <td>
                        <textarea
                               maxlength="500" name="paystack_thank_you_message"><?php echo esc_attr(get_option('paystack_thank_you_message')); ?></textarea>
                        <p class="description">Enter the thank-you message to be displayed after a successful
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
            const settingsForm = document.getElementById('paystack-settings-form');

            settingsForm.addEventListener('submit', function (e) {
                let minDonation = parseInt(document.getElementsByName('paystack_min_donation')[0].value, 10);
                var maxDonation = parseInt(document.getElementsByName('paystack_max_donation')[0].value, 10);

                if (minDonation < 100) {
                    alert('Minimum donation amount must be 100 or more.');
                    e.preventDefault();
                }

                if (minDonation > maxDonation) {
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
    global $public_key;
    $public_key = get_option('paystack_public_key', 'pk_test_fc018c5f85c2c87a539d3f33cef343fc05089170');
    global $thank_you_message;
    $thank_you_message = get_option('paystack_thank_you_message', '<h4 class=\'text-success mb-3\'>Thank You for Your Donation!</h4><p>Your generosity is greatly appreciated. With your support, we can continue making a positive impact.</p>');
    global $currency;
    $currency = get_option('paystack_currency', 'NGN');    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css');
    wp_enqueue_script('paystack-script', 'https://js.paystack.co/v1/inline.js', array('jquery'), null, true);
    wp_enqueue_style('tailwind-css', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');


    wp_localize_script('paystack-script', 'paystack_vars', array(
        'public_key' => $public_key,
        'thank_you_message' => $thank_you_message,
        'currency' => $currency
    ));
}

add_action('wp_enqueue_scripts', 'paystack_enqueue_scripts');

// Function to add shortcode
function fundflow_shortcode(): false|string
{

    $currency = get_option('paystack_currency', 'NGN');

    ob_start();
    ?>
    <div id="paystack-donation-form" class="container mt-2">
        <div class='text-center mb-3'>
            <p class='text-2x1 font-bold text-green-500'>We Accept Donations!</p>
            </p>
        </div>

        <div class="card  p-5 shadow max-w-md mx-auto rounded-lg">
            <form id="paystack-donation-form" class="needs-validation" novalidate>
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-600">Amount (<?php echo strtoupper($currency)?>)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span  class='text-gray-500 '><i class='fas fa-money-bill'></i></span>
                        </div>
                        <input placeholder="5000" type="number" class="form-input py-2 pl-8 w-full" name="amount"
                               id="amount" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input placeholder="example@email.com" type="email"
                               class="form-input py-2 pl-8 w-full is-next-40px-4"
                               name="email" id="email" required>
                    </div>
                </div>

                <button type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded-full w-full hover:bg-green-600 focus:outline-none focus:ring focus:border-blue-300">
                    Send Donation
                </button>

            </form>
        </div>
    </div>

    <div id="thank-you-card" class="card p-5 shadow max-w-md mx-auto hidden mb-4">
        <div class="text-center">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <p>
                <?php
                $thankYouMessage = esc_attr(get_option('paystack_thank_you_message'));
                echo !empty($thankYouMessage) ? $thankYouMessage : 'Thank you for your donation!';
                ?>
            </p>
        </div>
    </div>

    <div class='text-center mt-2'>
        <p class='text-sm text-gray -500'>Developed by <a href='mailto:eminibest@gmail.com' class='text-green-500'>Cyberwizard</a>
        </p>
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

            var paystackButton = document.querySelector('#paystack-donation-form button[type="submit"]');

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
                    currency: paystack_vars.currency.toUpperCase(),
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
add_shortcode('fundflow', 'fundflow_shortcode');
