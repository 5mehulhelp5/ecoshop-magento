<?php
namespace Magebees\Blog\Controller\Urlrewrite;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
class Fullurl extends \Magento\Framework\App\Action\Action {
    protected $resultRedirect;
	protected $_urlInterface;
	protected $request;
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		 \Magento\Framework\UrlInterface $urlInterface,
		 \Magento\Framework\App\Request\Http $request, 
		\Magento\Framework\Controller\ResultFactory $result) {
        $this->request = $request;
        parent::__construct($context);
        $this->resultRedirect = $result;
        $this->_urlInterface = $urlInterface;
    }
    public function execute() {
        $data = $this->request->getParams();
        if (isset($data['url'])) {
            $current_new_url = $data['url'];
            $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($current_new_url);
            return $resultRedirect;
        } else {
            $current_url = $this->_urlInterface->getUrl();
            $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($current_url);
            return $resultRedirect;
        }
    }
}

