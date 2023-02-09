<?php
namespace Dfe\Square\Controller\Index;
use Df\Framework\W\Result\Json;
/**     
 * 2016-09-28
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation. 
 * https://github.com/square/connect-api-examples/blob/f83e81/connect-examples/v1/php/webhooks.php
 * https://medium.com/square-corner-blog/webhooks-in-the-square-connect-api-d4d38c4b4d9f
 */
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * 2016-09-28 
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()  
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 */
	function execute():Json {return Json::i('OK');}
}