<?php
namespace Dfe\Square\Block;
use Dfe\Square\Method;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-10-06
 * @final
 * @method Method m()
 */
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