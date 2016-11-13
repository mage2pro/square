<?php
// 2016-09-28
namespace Dfe\Square;
use Df\Core\Exception as DFE;
use Magento\Framework\Exception\LocalizedException as LE;
use SquareConnect\Api\TransactionApi as API;
use SquareConnect\ApiException;
use SquareConnect\Model\Card;
use SquareConnect\Model\ChargeResponse;
use SquareConnect\Model\Tender;
use SquareConnect\Model\Transaction as SquareTransaction;
/** @method Settings s() */
class Method extends \Df\Payment\Method {
	/**
	 * 2016-09-30
	 * @override
	 * @see \Df\Payment\Method::charge()
	 * @param float $amount
	 * @param bool|null $capture [optional]
	 * @return void
	 */
	protected function charge($amount, $capture = true) {
		/** @var array(string => mixed) $params */
		$params = Charge::request($this, $this->iia(self::$TOKEN), $amount);
		/** @var ChargeResponse $response */
		$response = $this->api($params, function() use($params) {
			/** @var Settings $s */
			$s = $this->s();
			/** @noinspection PhpParamsInspection */
			return (new API)->charge($s->accessToken(), $s->location(), $params);
		});
		/** @var SquareTransaction $transaction */
		$transaction = $response->getTransaction();
		$this->ii()->setTransactionId($transaction->getId());
		$this->ii()->setIsTransactionClosed($capture);
		/** @var Tender $tender */
		$tender = df_first($transaction->getTenders());
		/** @var Card $card */
		$card = $tender->getCardDetails()->getCard();
		$this->ii()->setCcLast4($card->getLast4());
		$this->ii()->setCcType($card->getCardBrand());
	}

	/**
	 * 2016-09-28
	 * @override
	 * @see \Df\Payment\Method::iiaKeys()
	 * @used-by \Df\Payment\Method::assignData()
	 * @return string[]
	 */
	protected function iiaKeys() {return [self::$TOKEN];}

	/**
	 * 2016-10-06
	 * Чтобы система показала наше сообщение вместо общей фразы типа
	 * «We can't void the payment right now» надо вернуть объект именно класса
	 * @uses \Magento\Framework\Exception\LocalizedException
	 * https://mage2.pro/t/945
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Controller/Adminhtml/Order/VoidPayment.php#L20-L30
	 * @param array(callable|array(string => mixed)) ... $args
	 * @return mixed
	 * @throws DFE|Exception|LE
	 */
	private function api(...$args) {
		/** @var callable $function */
		/** @var array(string => mixed) $request */
		$args += [1 => []];
		list($function, $request) = is_callable($args[0]) ? $args : array_reverse($args);
		try {return $function();}
		catch (DFE $e) {throw $e;}
		catch (ApiException $e) {throw new Exception($e, $request);}
		catch (\Exception $e) {throw df_le($e);}
	}

	/**
	 * 2016-09-28
	 * @var string
	 */
	private static $TOKEN = 'token';
}