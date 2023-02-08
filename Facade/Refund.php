<?php
namespace Dfe\Square\Facade;
use Df\API\Operation;
# 2017-10-09
final class Refund extends \Df\StripeClone\Facade\Refund {
	/**
	 * 2017-10-09
	 * Note 1.
	 * Метод должен вернуть идентификатор операции (не платежа!) в платёжной системе.
	 * Мы записываем его в БД и затем при обработке оповещений от платёжной системы
	 * смотрим, не было ли это оповещение инициировано нашей же операцией,
	 * и если было, то не обрабатываем его повторно.
	 *
	 * Note 2.
	 * [Square] An example of a response to
	 * `POST /v2/locations/{location_id}/transactions/{transaction_id}/refund`: https://mage2.pro/t/4655
	 *	{
	 *		"amount_money": {"amount": 100, "currency": "USD"},
	 *		"created_at": "2016-02-12T00:28:18Z",
	 *		"id": "b27436d1-7f8e-5610-45c6-417ef71434b4-SW",
	 *		"location_id": "18YC4JDH91E1H",
	 *		"reason": "some reason",
	 *		"status": "PENDING",
	 *		"tender_id": "TENDER_ID",
	 *		"transaction_id": "TRANSACTION_ID"
	 *	}
	 *
	 * Note 3. «Connect API v2 Reference» → «Data Types» → «Refund»
	 * https://docs.connect.squareup.com/api/connect/v2#type-refund
	 * `id`: «The refund's unique ID».
	 *
	 * 2017-02-14
	 * Этот же идентификатор должен возвращать @see \Dfe\Stripe\W\Handler\Charge\Refunded::eTransId()
	 *
	 * @override
	 * @see \Df\StripeClone\Facade\Refund::transId()
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @param Operation $r
	 * Пример результата: «txn_19deRAFzKb8aMux1TLBWx6ZO».
	 */
	function transId($r):string {return $r['id'];}
}