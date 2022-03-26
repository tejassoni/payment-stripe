<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    
    public function __construct()
    {
        // initialize stripe 
        \Stripe\Stripe::setApiKey(config('stripe_constant.STRIPE_SECRET'));
    }

    // view checkout page with stripe intent and customer create
    public function viewCheckout(Request $request)
    {
        try {
            $customer_details = $this->stripeCreateCustomer();
            $amount = 100;
            $amount *= 100;
            $amount = (int) $amount;

            $payment_intent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'description' => 'test stripe payment',
                'payment_method_types' => ['card'],
                // Verify your integration in this guide by including this parameter
                'metadata' => ['integration_check' => 'accept_a_payment', 'order_id' => Str::random(6)],
                'customer' => $customer_details
            ]);
            $request->session()->put('payment_intent_id', $payment_intent->id);

            $intent = $payment_intent->client_secret;
            return view('checkout.credit-card', compact('intent'));
        } catch (\Stripe\Exception\CardException $e) {
            // Since it's a decline, \Stripe\Exception\CardException will be caught
            echo 'CardException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
            echo 'RateLimitException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            echo 'InvalidRequestException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            echo 'AuthenticationException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            echo 'ApiConnectionException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display a very generic error to the user, and maybe send
            echo 'ApiErrorException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
            // yourself an email
        } catch (\Exception $e) {
            // Something else happened, completely unrelated to Stripe
            echo 'Exception \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        }
    }

    // create stripe payment intent customer
    private function stripeCreateCustomer()
    {
        return \Stripe\Customer::create([
            'name' => 'David Millar',
            'phone' => '9898989898',
            'email' => 'davidstripe@yopmail.com',
            'address' =>   [
                'city' => 'San Francisco',
                'country' => 'US',
                'line1' => '965 Mission St #572',
                'line2' => '965 Mission St #572',
                'postal_code' => '94103',
                'state' => 'CA',
            ],
            // "balance"=> 15, // customer have wallet balance pass here
            'description' => 'New Customer created for Stripe payment',
        ]);
    }

    // after stripe payment operations
    public function afterPayment(Request $request)
    {
        try {
            $payment_intent_response = \Stripe\PaymentIntent::retrieve($request->session()->get('payment_intent_id'));
            if (isset($payment_intent_response->status) && $payment_intent_response->status == "succeeded") {
                $message = 'Thanks you, Payment Has been Received';
                
            } else {
                $message = 'Sorry, Payment not Received';                
            }
            return view('thank-you',\compact('message'));
        } catch (\Stripe\Exception\CardException $e) {
            // Since it's a decline, \Stripe\Exception\CardException will be caught
            echo 'CardException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
            echo 'RateLimitException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            echo 'InvalidRequestException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            echo 'AuthenticationException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            echo 'ApiConnectionException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display a very generic error to the user, and maybe send
            echo 'ApiErrorException \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
            // yourself an email
        } catch (\Exception $e) {
            // Something else happened, completely unrelated to Stripe
            echo 'Exception \n';
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
        }
    }
}
