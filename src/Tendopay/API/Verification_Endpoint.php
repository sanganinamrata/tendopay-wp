<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 04.08.2018
 * Time: 06:40
 */

namespace Tendopay\API;


use InvalidArgumentException;
use Tendopay\Exceptions\TendoPay_Integration_Exception;
use Tendopay\Gateway;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Verification_Endpoint
 * @package Tendopay\API
 */
class Verification_Endpoint {
	/**
	 * @param \WC_Order $order
	 * @param array $data
	 *
	 * @return bool
	 * @throws TendoPay_Integration_Exception
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function verify_payment( \WC_Order $order, array $data ) {
		ksort( $data );

		$gateway_options = get_option( "woocommerce_" . Gateway::GATEWAY_ID . "_settings" );
		$hash_calculator = new Hash_Calculator( $gateway_options['tendo_secret'] );

		$hash = $data['hash'];

		if ( $hash !== $hash_calculator->calculate( $data ) ) {
			throw new InvalidArgumentException( "Hash doesn't match" );
		}

		$disposition                  = $data['disposition'];
		$tendo_pay_transaction_number = $data['tendo_pay_transaction_number'];
		$verification_token           = $data['verification_token'];

		$verification_data = [
			'customer_reference_1'         => (string) $order->get_id(),
			'customer_reference_2'         => $order->get_order_key(),
			'disposition'                  => $disposition,
			'tendo_pay_merchant_id'        => (string) $gateway_options['tendo_pay_merchant_id'],
			'tendo_pay_transaction_number' => (string) $tendo_pay_transaction_number,
			'verification_token'           => $verification_token
		];

		$endpoint_caller = new Endpoint_Caller();
		$response        = $endpoint_caller->do_call( Tendopay_API::get_verification_endpoint_uri(),
			$verification_data );

		if ( $response->get_code() !== 200 ) {
			throw new TendoPay_Integration_Exception(
				"Received error: [{$response->get_code()}] while trying to verify the transaction" );
		}

		$json = json_decode( $response->get_body() );

		return $json->status === 'success';
	}
}
