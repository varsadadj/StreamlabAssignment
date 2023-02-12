<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBraintreeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
		    $table->string('braintree_id')->nullable()->after('password');
		    $table->string('paypal_email')->nullable()->after('braintree_id');
            $table->string('braintree_nonce')->nullable()->after('paypal_email');
            $table->string('card_brand')->nullable()->after('paypal_email');
		    $table->string('card_last_four')->nullable()->after('card_brand');
		    $table->timestamp('trial_ends_at')->nullable()->after('card_last_four');
		});

		Schema::create('subscriptions', function ($table) {
		    $table->increments('id');
		    $table->integer('user_id');
		    $table->string('name');
		    $table->string('braintree_id');
		    $table->string('braintree_plan');
		    $table->integer('quantity');
		    $table->timestamp('trial_ends_at')->nullable();
		    $table->timestamp('ends_at')->nullable();
		    $table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    	Schema::table('users', function (Blueprint $table) {
		    $table->dropColumn(['braintree_id', 'paypal_email', 'card_brand','card_last_four','trial_ends_at']);
		});
        Schema::dropIfExists('subscriptions');
    }
}
