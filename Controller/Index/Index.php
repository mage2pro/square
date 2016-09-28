<?php
// 2016-09-28
namespace Dfe\Square\Controller\Index;
use Df\Framework\Controller\Result\Json;
// 2016-09-28
// https://github.com/square/connect-api-examples/blob/f83e81/connect-examples/v1/php/webhooks.php
// https://medium.com/square-corner-blog/webhooks-in-the-square-connect-api-d4d38c4b4d9f
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * 2016-09-28
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return Json
	 */
	public function execute() {return Json::i('OK');}
}