<?php
// 2016-09-28
namespace Dfe\Square;
/** @method Settings s() */
class ConfigProvider extends \Df\Payment\ConfigProvider {
	/**
	 * 2016-09-28
	 * @override
	 * @see \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @return array(string => mixed)
	 */
	protected function config() {return [
		'applicationID' => $this->s()->applicationID()
	] + parent::config();}
}