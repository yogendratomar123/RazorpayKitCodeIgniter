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

$razorpay_payment_id = $response['razorpay_payment_id'];
$razorpay_order_id = $response['razorpay_order_id'];
$razorpay_signature = $response['razorpay_signature'];
$payment = $api->payment->fetch($razorpay_payment_id); 
//echo '<pre>'; print_r($payment); die;
if ($payment['status'] == 'captured') {
    /*
     * insert payment status into table
     */
    $paymentLaser = [
        'payment_id' => $payment['id'],
        'amount' => $payment['amount']/100,
        'status' => $payment['status'],
        'bank_name' => $payment['bank'],
        'response_msg' => $payment['description'], 
        'signature_hash' => $razorpay_signature, 
        'order_id' => $payment['order_id']
    ];
    insertPaymentLaser($paymentLaser);
    redirect(base_url('myaccount'));
     
}else{
     $paymentLaser = [
        'payment_id' => $payment['id'],
        'amount' => $payment['amount']/100,
        'status' => $payment['status'],
        'bank_name' => $payment['bank'],
        'response_msg' => $payment['description'], 
        'signature_hash' => $razorpay_signature, 
        'order_id' => $payment['order_id']  
    ];
    insertPaymentLaser($paymentLaser);
}
 
 
