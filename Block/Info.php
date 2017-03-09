<?php
namespace Dfe\Square\Block;
// 2016-10-06
/** @final Unable to use the PHP «final» keyword because of the M2 code generation. */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2016-10-06
	 * @override
	 * @see \Df\Payment\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 */
	final protected function prepare() {$i = $this->ii(); $this->si(
		'Card', df_cc_s($i->getCcType(), '****' . $i->getCcLast4())
	);}
}