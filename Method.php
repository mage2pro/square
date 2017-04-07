<?php
// 2016-09-28
namespace Dfe\Square;
use Df\Core\Exception as DFE;
use Df\Payment\Token;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order\Payment as OP;
use SquareConnect\Api\TransactionApi as API;
use SquareConnect\ApiException;
use SquareConnect\Model\Card;
use SquareConnect\Model\ChargeResponse;
use SquareConnect\Model\Tender;
use SquareConnect\Model\Transaction as SquareTransaction;
/** @method Settings s() */
final class Method extends \Df\Payment\Method {
	/**
	 * 2016-12-22
	 * https://code.dmitry-fedyuk.com/m2e/square/issues/6
	 * https://www.sellercommunity.com/t5/Developers-API/Connect-API-v2-What-are-the-minimum-and-maximum-limits-for/m-p/26939#M346
	 * https://mage2.pro/t/2411
	 * @override
	 * @see \Df\Payment\Method::amountLimits()
	 * @used-by isAvailable()
	 * @return array(string => array(int|float))
	 */
	protected function amountLimits() {return ['USD' => [1, 50000], 'CAD' => [1, 50000]];}

	/**
	 * 2016-09-30
	 * @override
	 * @see \Df\Payment\Method::charge()
	 * @used-by \Df\Payment\Method::authorize()
	 * @used-by \Df\Payment\Method::capture()
	 * @param float $amount
	 * @param bool|null $capture [optional]
	 */
	protected function charge($amount, $capture = true) {
		/** @var array(string => mixed) $p */
		$p = Charge::p($this, $amount);
		/** @var II|I|OP|QP $ii */
		$ii = $this->ii();
		/** @var ChargeResponse $response */
		$response = $this->api($p, function() use($p) {
			/** @var Settings $s */
			$s = $this->s();
			/** @noinspection PhpParamsInspection */
			return (new API)->charge($s->accessToken(), $s->location(), $p);
		});
		/** @var SquareTransaction $transaction */
		$transaction = $response->getTransaction();
		$ii->setTransactionId($transaction->getId());
		/**
		 * 2016-03-15
		 * Если оставить открытой транзакцию «capture»,
		 * то операция «void» (отмена авторизации платежа) будет недоступна:
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L540-L555
		 * @used-by \Magento\Sales\Model\Order\Payment::canVoid()
		 * Транзакция считается закрытой, если явно не указать «false».
		 *
		 * 2017-01-16
		 * Наоборот: если закрыть транзакцию типа «authorize»,
		 * то операция «Capture Online» из административного интерфейса будет недоступна:
		 * @see \Magento\Sales\Model\Order\Payment::canCapture()
		 *		if ($authTransaction && $authTransaction->getIsClosed()) {
		 *			$orderTransaction = $this->transactionRepository->getByTransactionType(
		 *				Transaction::TYPE_ORDER,
		 *				$this->getId(),
		 *				$this->getOrder()->getId()
		 *			);
		 *			if (!$orderTransaction) {
		 *				return false;
		 *			}
		 *		}
		 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment.php#L263-L281
		 * «How is \Magento\Sales\Model\Order\Payment::canCapture() implemented and used?»
		 * https://mage2.pro/t/650
		 * «How does Magento 2 decide whether to show the «Capture Online» dropdown
		 * on a backend's invoice screen?»: https://mage2.pro/t/2475
		 */
		$ii->setIsTransactionClosed($capture);
		/** @var Tender $tender */
		$tender = df_first($transaction->getTenders());
		/** @var Card $card */
		$card = $tender->getCardDetails()->getCard();
		$ii->setCcLast4($card->getLast4());
		$ii->setCcType($card->getCardBrand());
	}

	/**
	 * 2016-09-28
	 * @override
	 * @see \Df\Payment\Method::iiaKeys()
	 * @used-by \Df\Payment\Method::assignData()
	 * @return string[]
	 */
	protected function iiaKeys() {return [Token::KEY];}

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
}