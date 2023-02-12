<?php

namespace App\Http\Controllers;

use Braintree\ClientToken;

class BraintreeTokenController extends Controller
{
    //
    public function token()
    {
        return response()->json([
            'data' => [
                'token' => ClientToken::generate(),
            ]
        ]);
    }
}
