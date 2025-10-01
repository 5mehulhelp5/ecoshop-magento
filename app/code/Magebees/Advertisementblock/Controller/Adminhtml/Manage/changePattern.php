<?php

namespace  Magebees\Advertisementblock\Controller\Adminhtml\Manage;

use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class changePattern extends \Magento\Backend\App\Action
{

	protected $assetRepo;
	protected $_storeManager;
	protected $request;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Repository $assetRepo,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
         parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->request = $context->getRequest();
        $this->assetRepo = $assetRepo;
    }
    public function execute()
    {
         $result = [];
        $pattern_value= $this->getRequest()->getParam('pattern');
        $params = ['_secure' => $this->request->isSecure()];
        $img_name=$pattern_value.'.jpg';
        $url=$this->assetRepo->getUrlWithParams('Magebees_Advertisementblock::images/'.$img_name, $params);
        $resultFactory= $this->_objectManager->create('\Magento\Framework\View\Result\PageFactory');
        $resultPage= $resultFactory->create();
        $layoutblk = $resultPage->addHandle('advertisementblock_manage_edit')->getLayout();
        $form_content= $layoutblk->getBlock('magebees_advertisementblock_dynamicform')->toHtml();
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $patternHTML='<img src="'.$url.'" title="Pattern"/>';
        $result['pattern'] = $patternHTML;
        $result['form_content'] = $form_content;
        $resultJson->setData($result);
        return $resultJson;
        
        //print_r($patternHTML);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Advertisementblock::advertisementblock');
    }
}
