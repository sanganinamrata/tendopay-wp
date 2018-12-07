<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 04.08.2018
 * Time: 06:35
 */

namespace TendoPay;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

use WC_Order;
use WC_Order_Item;
use WC_Product;

/**
 * This class helps converting woocommerce order into an array, that will is later converted into JSON used for
 * description endpoint.
 *
 * @package TendoPay
 */
class Woocommerce_Order_Description_Retriever {
	/** @var WC_Order $order */
	private $order;


	/**
	 * Initializes class' fields.
	 *
	 * @param WC_Order $order Order from which to retrieve data
	 */
	public function __construct( WC_Order $order ) {
		$this->order = $order;
	}

	/**
	 * Transforms order details into an array ready to be encoded as JSON to be used for description endpoint in the
	 * following form:
	 *
	 * ```
	 * {
	 *   "items":
	 *     [{
	 *       "id": 123,
	 *       "title":"Beanie",
	 *       "description": "this is a Beanie",
	 *       "SKU": "1234", //optional
	 *       "price": "40.00",
	 *       "quantity": "2"
	 *     }]
	 * }
	 * ```
	 *
	 * @return array order details in form of an array
	 */
	public function get_order_details() {
		$fees_and_items = array_merge(
			array_values( $this->order->get_fees() ),
			array_values( $this->order->get_items() )
		);

		return apply_filters(
			'tendopay_order_details',
			[
				Constants::ITEMS_DESC_PROPNAME => array_map( [ $this, 'create_line_item' ], $fees_and_items ),
				Constants::META_DESC_PROPNAME  => $this->create_meta(),
				Constants::ORDER_DESC_PROPNAME => $this->create_order_details()
			],
			$this->order
		);
	}

	/**
	 * Creates single line item in the description array in the following form:
	 * ```
	 * {
	 *   "id": 123,
	 *   "title":"Beanie",
	 *   "description": "this is a Beanie",
	 *   "SKU": "1234", //optional
	 *   "price": "40.00",
	 *   "quantity": "2"
	 * }
	 * ```
	 *
	 * @param WC_Order_Item $line_item line item from the order
	 *
	 * @return array converted line item for the description doc
	 */
	private function create_line_item( WC_Order_Item $line_item ) {
		$line_item_data = $line_item->get_data();

		if ( $line_item instanceof \WC_Order_Item_Fee ) {
			$description = $line_item_data['name'];
			$sku         = null;
		} else {
			/** @var WC_Product $product */
			$product     = wc_get_product( $line_item_data['product_id'] );
			$description = $product->get_description();
			$sku         = $product->get_sku();
		}

		return apply_filters(
			'tendopay_description_line_item',
			[
				Constants::TITLE_ITEM_PROPNAME => $line_item_data['name'],
				Constants::DESC_ITEM_PROPNAME  => $description,
				Constants::SKU_ITEM_PROPNAME   => $sku,
				Constants::PRICE_ITEM_PROPNAME => $line_item_data['total'] + $line_item_data['total_tax'],
			],
			$line_item );
	}

	private function create_meta() {
		return apply_filters( 'tendopay_description_meta', [
			Constants::CURRENCY_META_PROPNAME     => get_woocommerce_currency(),
			Constants::THOUSAND_SEP_META_PROPNAME => wc_get_price_thousand_separator(),
			Constants::DECIMAL_SEP_META_PROPNAME  => wc_get_price_decimal_separator(),
			Constants::VERSION_META_PROPNAME      => '1'
		] );
	}

	private function create_order_details() {
		$subtotal_with_shipping = ( $this->order->get_total() - $this->order->get_total_tax()
		                            + $this->order->get_total_discount( true ) );

		return apply_filters( 'tendopay_description_meta', [
			Constants::ID_ORDER_PROPNAME       => $this->order->get_id(),
			Constants::SHIPPING_ORDER_PROPNAME => $this->order->get_total_shipping(),
			Constants::SUBTOTAL_ORDER_PROPNAME => $subtotal_with_shipping,
			Constants::TOTAL_ORDER_PROPNAME    => $this->order->get_total(),
		] );
	}
}
