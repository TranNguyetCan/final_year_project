<?php

namespace App\Service;

use Exception;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;


class PaypalService
{
    private $apiContext;

    public function __construct($clientId, $clientSecret, $mode)
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential($clientId, $clientSecret)
        );

        $this->apiContext->setConfig(['mode' => $mode]);
        
    }

    public function createPayment($total, $currency, $description, $returnUrl, $cancelUrl)
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $amount = new Amount();
        $amount->setTotal($total);
        $amount->setCurrency($currency);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription($description);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)->setCancelUrl($cancelUrl);

        $payment = new Payment();
        $payment->setIntent("sale")
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->apiContext);
            return $payment;
        } catch (Exception $e) {
            throw new \Exception('Unable to create PayPal payment: ' . $e->getMessage());
        }
    }


    public function executePayment($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            return $payment->execute($execution, $this->apiContext);
        } catch (Exception $e) {
            throw new \Exception('Unable to execute PayPal payment: ' . $e->getMessage());
        }
    }
   
}
