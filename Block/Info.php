<?php
namespace Dfe\Square\Block;
// 2016-10-06
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Info extends \Df\StripeClone\Block\Info {
	/**
	 * 2016-10-06
	 * @override
	 * @see \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 */
	final protected function prepare() {
		if ($this->tm()->res0()) {
			parent::prepare();
		}
		else {
			// 2017-10-10 This branch is for legacy transactions
			// (before the `Df_StripeClone` module usage).
			$i = $this->ii();
			$this->si('Card', df_cc_s($i->getCcType(), '****' . $i->getCcLast4()));
		}
	}
}