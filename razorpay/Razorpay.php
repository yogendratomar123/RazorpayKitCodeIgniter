<?php
// Include Requests only if not already defined
if (class_exists('Requests') === false) {
    require_once __DIR__ . '/libs/Requests-1.7.0/library/Requests.php';
}

try {
    Requests::register_autoloader();

    if (version_compare(Requests::VERSION, '1.6.0') === -1) {
        throw new Exception('Requests class found but did not match');
    }
} catch (\Exception $e) {
    throw new Exception('Requests class found but did not match');
}

spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'Razorpay\Api';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    //
    // replace the namespace prefix with the base directory,
    // replace namespace separators with directory separators
    // in the relative class name, append with .php
    //
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

use Razorpay\Api\Api;

$keyId = $razorpay_key['keyId'];
$secretKey = $razorpay_key['secretKey'];
$api = new Api($keyId, $secretKey);

$order = $api->order->create(array(
    'receipt' => rand(1000, 9999) . 'ORD',
    'amount' => $inputs['PAY_AMT'] * 100,
    'payment_capture' => 1,
    'currency' => 'INR',
        )
);
$name = $inputs['CUSTOMER_NAME'];
$logo = base_url() . 'public/myinboxhub_logo.png';
$email = $inputs['CUSTOMER_EMAIL'];
$mobile = $inputs['CUSTOMER_MOBILE'];
?>
<meta name="viewport" content="width=device-width">  
<style>
    .razorpay-payment-button{
        position: absolute;
        height: 0;
        width: 0;
        display: none;
    }
</style>
<form action="<?php echo base_url(); ?>payment/success/" method="POST">  
    <script
        src="https://checkout.razorpay.com/v1/checkout.js"
        data-key="<?php echo $keyId ?>"  
        data-amount="<?php echo $order->amount ?>" 
        data-currency="INR"
        data-order_id="<?php echo $order->id ?>" 
        data-buttontext="Pay with Razorpay"
        data-name="Myinboxhub"
        data-description="Donate for Myinboxhub"
        data-image="<?php echo $logo; ?>"
        data-prefill.name="<?php echo $name; ?>"
        data-prefill.email="<?php echo $email; ?>"
        data-prefill.contact="<?php echo $mobile; ?>"
        data-theme.color="#f0a43c"
    ></script>
    <input type="hidden" custom="Hidden Element" name="hidden">
</form>
<center>
    <div class=""><h1>Do not refresh or press back button</h1></div>
    <h3><a href="<?php echo base_url(); ?>payment/failed">Cancel Payment</a></h3>
</center>
<script>document.querySelector('.razorpay-payment-button').click();</script>

