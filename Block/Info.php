<?php
// 2016-10-06
namespace Dfe\Square\Block;
use Dfe\Square\Method;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/** @method Method m() */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2016-10-06
	 * @override
	 * @see \Df\Payment\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 */
	protected function prepare() {
		$this->si('Card', df_cc_s($this->ii()->getCcType(), '****' . $this->ii()->getCcLast4()));
	}
}