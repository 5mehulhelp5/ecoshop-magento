<?php
namespace Magebees\Layerednavigation\Controller\Adminhtml\Brands;
 
class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_scopeConfig;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_scopeConfig = $scopeConfig;
    }
    public function execute()
    {
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Layerednavigation::brand');
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->_view->loadLayout();
     
       
        $this->_addBreadcrumb(__('Magebees_Layernavigation'), __('Magebees_Layernavigation'));
        $this->_addBreadcrumb(__('Manage Brands'), __('Manage Brands'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Brands'));
        
        $helper = $this->_objectManager->create('Magebees\Layerednavigation\Helper\Data');
        $flag = $this->getRequest()->getParam('flag');

         $attributeCodes = $this->_scopeConfig->getValue('layerednavigation/brands/brand_attribute_code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $attributeCodes = explode(",",(string)$attributeCodes);

        foreach ($attributeCodes as $attributeCode) {
            $attribute_details = $helper->getBrandAttributeCode($attributeCode);
       
        
        if ($attribute_details=="attributeNotExist") {
            $this->messageManager->addError('You have Inserted Wrong attribute code under configuration setting.Please add correct one.');
        } else {
            $options = $attribute_details->getSource()->getAllOptions(false);
            $i=0;
            $option_ids = [];
            foreach ($options as $option) {
                $option_ids[$i]['label'] = $option["label"];
                $option_ids[$i]['value'] = $option["value"];
                $i++;
            }
            
            if (empty($option_ids)) {
                $this->messageManager->addError('Brand option values not available. Please insert Brand values');
            } else {
                $collection = $this->_objectManager->create('Magebees\Layerednavigation\Model\Brands')->getCollection()->addFieldToFilter('brand_code',$attributeCode);
                $coll = [];
                $coll = $collection->getData();
                            
                if (count($coll) == 0 && !isset($flag)) {
                    $this->messageManager->addWarning('Please click Load Brand button to show listing of brands');
                } elseif (count($coll) != 0 && isset($flag)) {
                    $this->messageManager->getMessages(true);
                } else {
                    $this->messageManager->getMessages(true);
                }
            }
        }
         }

       // $this->_view->renderLayout();
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::brand');
    }
}
