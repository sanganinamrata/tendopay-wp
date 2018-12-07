<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 05.08.2018
 * Time: 05:59
 */

namespace TendoPay;

use TendoPay\Gateway;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Configuration class.
 *
 * @package TendoPay\API
 */
class Constants {
	const PAYMANET_FAILED_QUERY_PARAM = 'tendopay_payment_failed';

	const REDIRECT_URL_PATTERN = '^tendopay-result/?';

	const HASH_ALGORITHM = 'sha256';

	const BASE_API_URL = 'https://app.tendopay.ph';
	const SANDBOX_BASE_API_URL = 'https://sandbox.tendopay.ph';

	const REDIRECT_URI = 'https://app.tendopay.ph/payments/authorise';
	const VIEW_URI_PATTERN = 'https://app.tendopay.ph/view/transaction/%s';
	const VERIFICATION_ENDPOINT_URI = 'payments/api/v1/verification';
	const AUTHORIZATION_ENDPOINT_URI = 'payments/api/v1/authTokenRequest';
	const DESCRIPTION_ENDPOINT_URI = 'payments/api/v1/paymentDescription';
	const BEARER_TOKEN_ENDPOINT_URI = 'oauth/token';

	const SANDBOX_REDIRECT_URI = 'https://sandbox.tendopay.ph/payments/authorise';
	const SANDBOX_VIEW_URI_PATTERN = 'https://sanbox.tendopay.ph/view/transaction/%s';
	const SANDBOX_VERIFICATION_ENDPOINT_URI = 'payments/api/v1/verification';
	const SANDBOX_AUTHORIZATION_ENDPOINT_URI = 'payments/api/v1/authTokenRequest';
	const SANDBOX_DESCRIPTION_ENDPOINT_URI = 'payments/api/v1/paymentDescription';
	const SANDBOX_BEARER_TOKEN_ENDPOINT_URI = 'oauth/token';

	/**
	 * Below constant names are used as keys of data send to or received from TP API
	 */
	const AMOUNT_PARAM = 'tendopay_amount';
	const AUTH_TOKEN_PARAM = 'tendopay_authorisation_token';
	const ORDER_ID_PARAM = 'tendopay_customer_reference_1';
	const ORDER_KEY_PARAM = 'tendopay_customer_reference_2';
	const REDIRECT_URL_PARAM = 'tendopay_redirect_url';
	const VENDOR_ID_PARAM = 'tendopay_tendo_pay_vendor_id';
	const VENDOR_PARAM = 'tendopay_vendor';
	const HASH_PARAM = 'tendopay_hash';
	const DISPOSITION_PARAM = 'tendopay_disposition';
	const TRANSACTION_NO_PARAM = 'tendopay_transaction_number';
	const VERIFICATION_TOKEN_PARAM = 'tendopay_verification_token';
	const DESC_PARAM = 'tendopay_description';
	const STATUS_PARAM = 'tendopay_status';
	const USER_ID_PARAM = 'tendopay_user_id';

	const TENDOPAY_ICON = 'https://s3.ca-central-1.amazonaws.com/candydigital/images/tendopay/tp-icon-32x32.png';
	const TENDOPAY_FAQ = 'https://tendopay.ph/page-faq.html';
	/**
	 * Below constants are the keys of description object that is being sent during request to Description Endpoint
	 */
	const ITEMS_DESC_PROPNAME = 'items';
	const META_DESC_PROPNAME = 'meta';
	const ORDER_DESC_PROPNAME = 'order';

	/**
	 * Below constants are the keys of description object's line items that are being sent during request to Description Endpoint
	 */
	const TITLE_ITEM_PROPNAME = 'title';
	const DESC_ITEM_PROPNAME = 'description';
	const SKU_ITEM_PROPNAME = 'SKU';
	const PRICE_ITEM_PROPNAME = 'price';

	/**
	 * Below constants are the keys of description object's meta info that is being sent during request to Description Endpoint
	 */
	const CURRENCY_META_PROPNAME = 'currency';
	const THOUSAND_SEP_META_PROPNAME = 'thousand_separator';
	const DECIMAL_SEP_META_PROPNAME = 'decimal_separator';
	const VERSION_META_PROPNAME = 'version';

	/**
	 * Below constants are the keys of description object's order details that are being sent during request to Description Endpoint
	 */
	const ID_ORDER_PROPNAME = 'id';
	const SHIPPING_ORDER_PROPNAME = 'shipping';
	const SUBTOTAL_ORDER_PROPNAME = 'subtotal';
	const TOTAL_ORDER_PROPNAME = 'total';

	/**
	 * Gets the hash algorithm.
	 *
	 * @return string hash algorithm
	 */
	public static function get_hash_algorithm() {
		return self::HASH_ALGORITHM;
	}

	/**
	 * Gets the base api URL. It checks whether to use SANDBOX URL or Production URL.
	 *
	 * @return string the base api url
	 */
	public static function get_base_api_url() {
		return self::is_sandbox_enabled() ? self::SANDBOX_BASE_API_URL : self::BASE_API_URL;
	}

	/**
	 * Gets the redirect uri. It checks whether to use SANDBOX URI or Production URI.
	 *
	 * @return string redirect uri
	 */
	public static function get_redirect_uri() {
		return self::is_sandbox_enabled() ? self::SANDBOX_REDIRECT_URI : self::REDIRECT_URI;
	}

	/**
	 * Gets the view uri pattern. It checks whether to use SANDBOX pattern or Production pattern.
	 *
	 * @return string view uri pattern
	 */
	public static function get_view_uri_pattern() {
		return self::is_sandbox_enabled() ? self::SANDBOX_VIEW_URI_PATTERN : self::VIEW_URI_PATTERN;
	}

	/**
	 * Gets the verification endpoint uri. It checks whether to use SANDBOX URI or Production URI.
	 *
	 * @return string verification endpoint uri
	 */
	public static function get_verification_endpoint_uri() {
		return self::is_sandbox_enabled() ? self::SANDBOX_VERIFICATION_ENDPOINT_URI : self::VERIFICATION_ENDPOINT_URI;
	}

	/**
	 * Gets the authorization endpoint uri. It checks whether to use SANDBOX URI or Production URI.
	 *
	 * @return string authorization endpoint uri
	 */
	public static function get_authorization_endpoint_uri() {
		return self::is_sandbox_enabled() ? self::SANDBOX_AUTHORIZATION_ENDPOINT_URI : self::AUTHORIZATION_ENDPOINT_URI;
	}

	/**
	 * Gets the description endpoint uri. It checks whether to use SANDBOX URI or Production URI.
	 *
	 * @return string description endpoint uri
	 */
	public static function get_description_endpoint_uri() {
		return self::is_sandbox_enabled() ? self::SANDBOX_DESCRIPTION_ENDPOINT_URI : self::DESCRIPTION_ENDPOINT_URI;
	}

	/**
	 * Gets the bearer token endpoint uri. It checks whether to use SANDBOX URI or Production URI.
	 *
	 * @return string bearer token endpoint uri
	 */
	public static function get_bearer_token_endpoint_uri() {
		return self::is_sandbox_enabled() ? self::SANDBOX_BEARER_TOKEN_ENDPOINT_URI : self::BEARER_TOKEN_ENDPOINT_URI;
	}

	/**
	 *
	 * @return bool true if sandbox is enabled
	 */
	private static function is_sandbox_enabled() {
		$gateway_options = get_option( "woocommerce_" . Gateway::GATEWAY_ID . "_settings" );

		return apply_filters( 'tendopay_sandbox_enabled',
			$gateway_options[ Gateway::OPTION_TENDOPAY_SANDBOX_ENABLED ] === 'yes' );
	}
}
