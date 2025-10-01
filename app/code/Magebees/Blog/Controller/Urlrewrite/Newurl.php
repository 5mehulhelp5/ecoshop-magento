<?php
namespace Magebees\Blog\Controller\Urlrewrite;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
class Newurl extends \Magento\Framework\App\Action\Action {
     protected $resultRedirect;
	protected $_urlInterface;
	protected $request;
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\UrlInterface $urlInterface,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Controller\ResultFactory $result) 
	{
        $this->request = $request;
        parent::__construct($context);
        $this->resultRedirect = $result;
        $this->_urlInterface = $urlInterface;
    }
    public function execute() {
        $data = $this->request->getParams();
        $current_url = $this->_urlInterface->getUrl();
        $current_url_explode = explode("/", (string)$current_url);
        $last_key = end($current_url_explode);
        $modified_key = array_search($last_key, $current_url_explode);
        if (isset($data['new_url'])) {
            $new_url = $data['new_url'];
            $current_new_url = $current_url . $new_url;
            $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($current_new_url);
            return $resultRedirect;
        } else {
            $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($current_url);
            return $resultRedirect;
        }
    }
}
