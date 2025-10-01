<?php
namespace Magebees\Layerednavigation\Controller\Adminhtml\Brands;

use Magento\Framework\App\Filesystem\DirectoryList;

class Saveattribute extends \Magento\Backend\App\Action
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
                if (isset($flag)) {
                    $this->messageManager->addError('There are no brand values available.Please Insert values and then click on load brand button');
                    $del_Model = $this->insert_brand($option_ids,$attributeCode);
                } else {
                    $this->messageManager->addError('Brand option values not available.Please insert values');
                    $del_Model = $this->insert_brand($option_ids,$attributeCode);
                }
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
                        
                if (isset($flag) && $flag == '1') {
                    $insertModel = $this->insert_brand($option_ids,$attributeCode);
                }
            }
        }
        }
        
         $this->_redirect('*/*/');
    }
    
    
    public function insert_brand($option_ids,$attributeCode)
    {
        $helper = $this->_objectManager->create('Magebees\Layerednavigation\Helper\Data');
        $brands = $this->_objectManager->create('Magebees\Layerednavigation\Model\Brands');
        try {
            if (empty($option_ids)) {
                $delete_data = $brands->getCollection()->addFieldToFilter('brand_code',$attributeCode);
                foreach ($delete_data as $item) {
                    $item->delete();
                }
            } else {
                $delete_data = $brands->getCollection()->addFieldToFilter('brand_code',$attributeCode)
                    ->addFieldToFilter('option_id', ['nin' => $option_ids]);
                $delete_data->walk('delete');
            }
                $insert = [];
            foreach ($option_ids as $insert_brand) {
                $insert['brand_name'] = $insert_brand['label'];
                $insert['option_id'] = $insert_brand['value'];
                $insert['brand_code'] = $attributeCode;
                $insert['status'] = 1;
                $insert['seo_url'] = $helper->getOptionUrl($insert_brand['label'], $insert['option_id']);
                $collection = $brands->getCollection()->addFieldToFilter('option_id', $insert_brand['value'])->addFieldToFilter('brand_code',$attributeCode)->getFirstItem();
                    
                $brand_id = $collection->getData('brand_id');
                if ($collection->getData('option_id')) {
                   
                    if ($collection->getData('brand_name') != $insert['brand_name']) {
                        $model = $brands->load($brand_id);
                        $model->setData('brand_name', $insert['brand_name']);
                         $model->setData('brand_code',$attributeCode);
                        $model->save();
                    }
                } else {                 
                    $model = $brands->setData($insert);
                    $insertId = $model->save()->getbrand_id();
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
    
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::brand');
    }
}
