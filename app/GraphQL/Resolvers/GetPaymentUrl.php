<?php

namespace App\GraphQL\Resolvers;

use App\Models\Order;
use App\Services\TBankMerchantAPI;
use App\Services\TotalsService;

readonly class GetPaymentUrl
{
    public function __construct(private TotalsService $totalsService)
    {
    }

    /**
     * @throws \HttpException
     */
    public function __invoke(Order $rootValue)
    {
        $config = config('tbank');
        $totals = $this->totalsService->getOrderTotals($rootValue);

        $requestData = array(
            'OrderId' => $rootValue->id,
            'Amount' => $this->getAmount($totals->grand_total->getRegularOrDiscounted()),
            'DATA' => array(
                'Email' => $rootValue->email
            ),
        );

        if ($config['language'] == 'en') {
            $requestData['Language'] = 'en';
        }

        if ($config['enabled_taxation']) {
            $receiptItems = array();
            $orderItems = $rootValue->items;

            if (!$config['ffd12']) {
                foreach ($orderItems as $item) {
                    $price = $item->getPrice();
                    $quantity = $item->quantity;
                    $receiptItems[] = array(
                        'Name' => mb_substr($item->product->name, 0, 64),
                        "Price" => $item->getPrice(),
                        "Quantity" => $quantity,
                        "Amount" => $this->getAmount(round($price * $quantity)),
                        "PaymentMethod" => trim($config['payment_method']),
                        "PaymentObject" => trim($config['payment_object']),
                        "Tax" => 0,
                    );
                }
            } else {
                foreach ($orderItems as $item) {
                    $price = $item->getPrice();
                    $quantity = $item->quantity;

                    $receiptItems[] = array(
                        'Name' => mb_substr($item->product->name, 0, 64),
                        "Price" => $price,
                        "Quantity" => $quantity,
                        "Amount" => $this->getAmount(round($price * $quantity)),
                        "PaymentMethod" => trim($config['payment_method']),
                        "PaymentObject" => trim($config['payment_object']),
                        "Tax" => 0,
                        "MeasurementUnit" => "pc"
                    );

                }
            }

            $shippingPrice = $totals->delivery_price;

            if ($shippingPrice?->isPositive()) {
                $shippingMethod = $rootValue->shipping_method;
                $shippingPriceValue = $shippingPrice->getRegularOrDiscounted();

                if (!$config['ffd12']) {
                    $receiptItems[] = array(
                        'Name' => mb_substr($shippingMethod->label, 0, 64),
                        "Price" => $shippingPriceValue,
                        "Quantity" => 1,
                        "Amount" => $this->getAmount($shippingPriceValue),
                        "PaymentMethod" => trim($config['payment_method']),
                        "PaymentObject" => "service",
                        "Tax" => 0,
                    );
                } else {
                    $receiptItems[] = array(
                        'Name' => mb_substr($shippingMethod->label, 0, 64),
                        "Price" => $shippingPriceValue,
                        "Quantity" => 1,
                        "Amount" => $this->getAmount($shippingPriceValue),
                        "PaymentMethod" => trim($config['payment_method']),
                        "PaymentObject" => "service",
                        "Tax" => 0,
                        "MeasurementUnit" => "pc"
                    );
                }
            }

            $emailCompany = substr($config['email_company'], 0, 64);
            if (!$emailCompany) {
                $emailCompany = null;
            }
            $requestData['Receipt'] = array(
                'EmailCompany' => $emailCompany,
                'Email' => $rootValue->email,
                'Taxation' => $config['taxation'],
                'Items' => $receiptItems,
            );

            if ($config['ffd12']) {
                $requestData['Receipt']['FfdVersion'] = "1.2";
            }
        }

        $TBank = new TBankMerchantAPI($config['merchant_id'], $config['secret_key']);
        $request = $TBank->buildQuery('Init', $requestData);
        $request = json_decode($request);

        return $request->PaymentURL ?? '';
    }

    public function getAmount(int $price): int
    {
        return $price * 100;
    }
}
