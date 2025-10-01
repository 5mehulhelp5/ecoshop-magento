<?php
namespace Magebees\Layerednavigation\Controller\Brand;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Brandlistsearch extends Action
{

   
    protected $resultPageFactory;
    protected $scopeConfig;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }
    
    public function execute()
    {
                
        
        
        $resultFactory= $this->_objectManager->create('\Magento\Framework\View\Result\PageFactory');
        $resultPage= $resultFactory->create();
        $layoutblk = $resultPage->addHandle('layerednavigation_brand_ajaxlist')->getLayout();
        $brand_content= $layoutblk->getBlock('ajax_brandinfo')->toHtml();
        $result = [];
        $result['brand'] = $brand_content;
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
}
