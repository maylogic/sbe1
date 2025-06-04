<?php

return [
    'merchant_id' => env('TBANK_MERCHANT_ID', ''),
    'secret_key' => env('TBANK_SECRET_KEY', ''),
    'enabled_taxation' => env('TBANK_ENABLED_TAXATION', 'usn_income'),
    'ffd12' => env('TBANK_FFD12', false),
    'email_company' => env('TBANK_EMAIL_COMPANY', 'artzwinger@bk.ru'),
    'payment_method' => env('TBANK_PAYMENT_METHOD', 'full_prepayment'),
    'payment_object' => env('TBANK_PAYMENT_OBJECT', 'commodity'),
    'taxation' => env('TBANK_TAXATION', '0'),
    'language' => env('TBANK_LANGUAGE', 'ru'),
];
