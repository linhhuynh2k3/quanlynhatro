<?php

$defaults = [
    'tmn_code' => 'X49PI1WG',
    'hash_secret' => 'LKSOAAPCIUWUKVZKDMXZVYPUTOQGXXPH',
    'url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
    'return_url_landlord' => env('APP_URL', 'http://127.0.0.1:8000') . '/landlord/payments/vnpay/return',
    'return_url_tenant' => env('APP_URL', 'http://127.0.0.1:8000') . '/bookings/vnpay/return',
    'ipn_url' => 'http://127.0.0.1:8000/landlord/payments/vnpay/ipn',
    'version' => '2.1.0',
    'locale' => 'vn',
    'curr_code' => 'VND',
    'order_type' => 'billpayment',
    'expire_minutes' => 15,
];

$envValue = function (string $primaryKey, ?string $secondaryKey, $default) {
    return env($primaryKey, $secondaryKey ? env($secondaryKey, $default) : $default);
};

return [
    'tmn_code' => $envValue('VNPAY_TMN_CODE', 'VNP_TMN_CODE', $defaults['tmn_code']),
    'hash_secret' => $envValue('VNPAY_HASH_SECRET', 'VNP_HASH_SECRET', $defaults['hash_secret']),
    'url' => env('VNPAY_URL', env('VNP_URL', $defaults['url'])),
    'return_url_landlord' => env('VNPAY_RETURN_URL_LANDLORD', env('VNP_RETURN_URL_LANDLORD', $defaults['return_url_landlord'])),
    'return_url_tenant' => env('VNPAY_RETURN_URL_TENANT', env('VNP_RETURN_URL_TENANT', $defaults['return_url_tenant'])),
    'ipn_url' => env('VNPAY_IPN_URL', $defaults['ipn_url']),
    'version' => env('VNPAY_VERSION', $defaults['version']),
    'locale' => env('VNPAY_LOCALE', $defaults['locale']),
    'curr_code' => env('VNPAY_CURR_CODE', $defaults['curr_code']),
    'order_type' => env('VNPAY_ORDER_TYPE', $defaults['order_type']),
    'expire_minutes' => env('VNPAY_EXPIRE_MINUTES', $defaults['expire_minutes']),
];

