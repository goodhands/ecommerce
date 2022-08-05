<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store\Order;
use App\Models\Store;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    private function productId($store)
    {
        if (count($store->products) > 1) {
            return $store->products()->inRandomOrder()->first()->id;
        }

        return 1;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $store = Store::find(3);

        $products = $store->products;

        $status = rand(0, 1);

        foreach ($products as $product) {
            // create order
            $order = Order::create(
                array(
                    'payment_method' => rand(1, 4),
                    'delivery_method' => rand(1, 4),
                    'customer_id' => rand(0, 10),
                    'total' => rand(1000, 99999),
                    'fulfilled' => rand(0, 1),
                    'payment_status' => $status === 1 ? 'Paid' : 'Pending',
                    'delivery_date' => Carbon::now(),
                    'delivery_address' => array(
                        "address" => "15 Latifat Street 74 309 AugustinaVille",
                        "apartment" => "Apartment 74",
                        "city" => "Abule Egba",
                        "state" => "Onitsha",
                        "country" => "Nigeria",
                    ),
                    'payment_data' => json_decode(
                        json_encode(
                            '{
                                "id": "pi_1DrPlP2eZvKYlo2CSBQ7uqFH",
                                "object": "payment_intent",
                                "amount": 1000,
                                "amount_capturable": 0,
                                "amount_details": {
                                "tip": {}
                                },
                                "amount_received": 1000,
                                "application": null,
                                "application_fee_amount": null,
                                "automatic_payment_methods": null,
                                "canceled_at": null,
                                "cancellation_reason": null,
                                "capture_method": "automatic",
                                "charges": {
                                "object": "list",
                                "data": [
                                    {
                                    "id": "ch_1EXUPv2eZvKYlo2CStIqOmbY",
                                    "object": "charge",
                                    "amount": 1000,
                                    "amount_captured": 1000,
                                    "amount_refunded": 0,
                                    "application": null,
                                    "application_fee": null,
                                    "application_fee_amount": null,
                                    "balance_transaction": "txn_1EXUPv2eZvKYlo2CNUI18wV8",
                                    "billing_details": {
                                        "address": {
                                        "city": null,
                                        "country": null,
                                        "line1": null,
                                        "line2": null,
                                        "postal_code": null,
                                        "state": null
                                        },
                                        "email": null,
                                        "name": null,
                                        "phone": null
                                    },
                                    "calculated_statement_descriptor": "Stripe",
                                    "captured": true,
                                    "created": 1557239835,
                                    "currency": "usd",
                                    "customer": null,
                                    "description": "One blue fish",
                                    "disputed": false,
                                    "failure_balance_transaction": null,
                                    "failure_code": null,
                                    "failure_message": null,
                                    "fraud_details": {},
                                    "invoice": null,
                                    "livemode": false,
                                    "metadata": {},
                                    "on_behalf_of": null,
                                    "outcome": {
                                        "network_status": "approved_by_network",
                                        "reason": null,
                                        "risk_level": "normal",
                                        "risk_score": 9,
                                        "seller_message": "Payment complete.",
                                        "type": "authorized"
                                    },
                                    "paid": true,
                                    "payment_intent": "pi_1DrPlP2eZvKYlo2CSBQ7uqFH",
                                    "payment_method": "pm_1EXUPv2eZvKYlo2CUkqZASBe",
                                    "payment_method_details": {
                                        "card": {
                                        "brand": "visa",
                                        "checks": {
                                            "address_line1_check": null,
                                            "address_postal_code_check": null,
                                            "cvc_check": null
                                        },
                                        "country": "US",
                                        "exp_month": 5,
                                        "exp_year": 2020,
                                        "fingerprint": "Xt5EWLLDS7FJjR1c",
                                        "funding": "credit",
                                        "installments": null,
                                        "last4": "4242",
                                        "mandate": null,
                                        "moto": null,
                                        "network": "visa",
                                        "three_d_secure": null,
                                        "wallet": null
                                        },
                                        "type": "card"
                                    },
                                    "receipt_email": null,
                                    "receipt_number": "1230-7299",
                                    "receipt_url": "https://pay.stripe.com/receipts/acct_1032D82eZvKYlo2C/ch_1EXUPv2eZvKYlo2CStIqOmbY/rcpt_F1XUd7YIQjmM5TVGoaOmzEpU0FBogb2",
                                    "redaction": null,
                                    "refunded": false,
                                    "refunds": {
                                        "object": "list",
                                        "data": [],
                                        "has_more": false,
                                        "url": "/v1/charges/ch_1EXUPv2eZvKYlo2CStIqOmbY/refunds"
                                    },
                                    "review": null,
                                    "shipping": null,
                                    "source_transfer": null,
                                    "statement_descriptor": null,
                                    "statement_descriptor_suffix": null,
                                    "status": "succeeded",
                                    "transfer_data": null,
                                    "transfer_group": null
                                    }
                                ],
                                "has_more": false,
                                "url": "/v1/charges?payment_intent=pi_1DrPlP2eZvKYlo2CSBQ7uqFH"
                                },
                                "client_secret": "pi_1DrPlP2eZvKYlo2CSBQ7uqFH_secret_9J35eTzWlxVmfbbQhmkNbewuL",
                                "confirmation_method": "automatic",
                                "created": 1524505326,
                                "currency": "usd",
                                "customer": null,
                                "description": "One blue fish",
                                "invoice": null,
                                "last_payment_error": null,
                                "livemode": false,
                                "metadata": {},
                                "next_action": null,
                                "on_behalf_of": null,
                                "payment_method": "pm_1EXUPv2eZvKYlo2CUkqZASBe",
                                "payment_method_options": {},
                                "payment_method_types": [
                                "card"
                                ],
                                "processing": null,
                                "receipt_email": null,
                                "redaction": null,
                                "review": null,
                                "setup_future_usage": null,
                                "shipping": null,
                                "statement_descriptor": null,
                                "statement_descriptor_suffix": null,
                                "status": "succeeded",
                                "transfer_data": null,
                                "transfer_group": null
                            }'
                        ),
                        true
                    )
                )
            );

            $store->orders()->save($order);

            $order->products()->attach($product->id, ['quantity' => rand(0, 9)]);
        }
    }
}
