<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Subscription;
use Braintree\Configuration;
use Braintree\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    //

    public function index()
    {
        $subscriptions = Subscription::whereNull('ends_at')->get();

        return view('subscriptions.index')->with([
            'subscriptions' => $subscriptions
        ]);
    }

    public function store(Request $request)
    {
        // get the plan after submitting the form
        $plan = Plan::findOrFail($request->plan);

        $user = auth()->user();
        $payment_method_nonce = $request->get('payment_method_nonce');

        if(!$user->braintree_nonce)
        {
            $customer_create_result = Configuration::gateway()->customer()->create([
                'firstName' => $user->name,
                'paymentMethodNonce' => $payment_method_nonce
            ]);

            // save this nonce to user table
            $user->braintree_nonce = $customer_create_result->customer->paymentMethods[0]->token;
            $user->save();
        }

        $subscription = Configuration::gateway()->subscription()->create([
            'paymentMethodToken' => $user->braintree_nonce,
            'planId' => $plan->braintree_plan,
        ]);


        if(!$subscription->success)
        {
            $errorString = "";

            foreach ($subscription->errors->deepAll() as $error)
            {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }
            return redirect()->back()->withErrors('An error occurred with the message: '.$errorString);
        }
        else
        {
            $braintree_id = $subscription->subscription->id;
            Subscription::create([
                'user_id' => $user->id,
                'name' => $plan->name,
                'braintree_id' => $braintree_id,
                'braintree_plan' => $plan->braintree_plan,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ]);

            $user->braintree_id = $braintree_id;
            $user->save();
        }

        // redirect to home after a successful subscription
        return redirect('home')->with('status', 'Subscription has been successfully created. The Subscription ID is:'. $braintree_id);
    }

    public function cancel(Request $request)
    {
        $subscription_id = $request->subscription_id;
        $subscription = Subscription::find($subscription_id);

        if(!$subscription)
        {
            return redirect('subscription')->withErrors("Subscription doesn't found");
        }

        $result = Configuration::gateway()->subscription()->cancel($subscription->braintree_id);
        if(!$result)
        {
            $errorString = "";

            foreach ($subscription->errors->deepAll() as $error)
            {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            return redirect()->back()->withErrors('An error occurred with the message: '.$errorString);
        }
        $subscription->ends_at = Carbon::now();
        $subscription->save();
        return redirect('subscription')->with('status', 'Subscription has been successfully deleted.');
    }
}
