<?php
// 2016-09-28
namespace Dfe\Square\Controller\Index;
use Df\Framework\Controller\Result\Json;
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * 2016-09-28
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return Json
	 */
	public function execute() {return Json::i('OK');}
}