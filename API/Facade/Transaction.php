<?php
namespace Dfe\Square\API\Facade;
use Df\API\Operation as O;
use Zend_Http_Client as Z;
/**
 * 2017-10-08
 * Note 1. «Connect API v2 Reference» → «Endpoints» → «Transactions»
 * https://docs.connect.squareup.com/api/connect/v2#navsection-transactions
 * Note 2. [Square] An example of a response to `POST /v2/locations/{location_id}/transactions`
 * https://mage2.pro/t/4648
 */
final class Transaction extends LocationBased {
	/**
	 * 2017-10-09 «Connect API v2 Reference» → «Endpoints» → «Transactions» → «CaptureTransaction»
	 * https://docs.connect.squareup.com/api/connect/v2#endpoint-capturetransaction
	 * @used-by \Dfe\Square\Facade\Charge::capturePreauthorized()
	 */
	function capture(string $id):O {return $this->p($id, Z::POST, 'capture');}

	/**
	 * 2017-10-09 «Connect API v2 Reference» → «Endpoints» → «Transactions» → «VoidTransaction»
	 * https://docs.connect.squareup.com/api/connect/v2#endpoint-voidtransaction
	 * @used-by \Dfe\Square\Facade\Charge::void()
	 */
	function void_(string $id):O {return $this->p($id, Z::POST, 'void');}

	/**
	 * 2017-10-09 «Connect API v2 Reference» → «Endpoints» → «Transactions» → «CreateRefund»
	 * https://docs.connect.squareup.com/api/connect/v2#endpoint-createrefund
	 * @used-by \Dfe\Square\Facade\Charge::refund()
	 * @param array(string => mixed) $p
	 */
	function refund(string $id, array $p):O {return $this->p([$id, $p], Z::POST, 'refund');}
}