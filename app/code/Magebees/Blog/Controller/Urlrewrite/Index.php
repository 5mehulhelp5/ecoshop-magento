<?php
namespace Magebees\Blog\Controller\Urlrewrite;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
class Index extends \Magento\Framework\App\Action\Action {
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
        $current_url = $this->_urlInterface->getCurrentUrl();
        $current_url_explode = explode("/", (string)$current_url);
        $last_key = end($current_url_explode);
        $modified_key = array_search($last_key, $current_url_explode);
        if (isset($current_url_explode[$modified_key])) {
            $new_url = $data['urlrewrite'];
            $last_url_arr = explode(".", (string)$current_url_explode[$modified_key]);
            $last_url_arr['0'] = $new_url;
            $new_last_url = implode(".", (array)$last_url_arr);
            $current_url_explode[$modified_key] = $new_last_url;
        }
        $current_new_url = implode("/", (array)$current_url_explode);
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($current_new_url);
        return $resultRedirect;
    }
}
