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
	 * @see \Magento\Payment\Block\ConfigurableInfo::_prepareSpecificInformation()
	 * @used-by \Magento\Payment\Block\Info::getSpecificInformation()
	 * @param DataObject|null $transport
	 * @return DataObject
	 */
	protected function _prepareSpecificInformation($transport = null) {
		/** @var DataObject $result */
		$result = parent::_prepareSpecificInformation($transport);
		$result['Card'] = df_cc_s($this->ii()->getCcType(), '****' . $this->ii()->getCcLast4());
		$this->markTestMode($result);
		return $result;
	}
}