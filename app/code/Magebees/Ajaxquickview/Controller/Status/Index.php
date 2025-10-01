<?php
namespace Magebees\Ajaxquickview\Controller\Status;
 
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
 
class Index extends Action
{
 
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    
    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context, 
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    )
    {
 
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
 
 
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $data1=array();
        $data = $this->getRequest()->getParams();
        foreach ($data as $value) {
            array_push($data1, $value);
        }
        $attribute_code=explode(',',(string)$data1['0']);
        $selected_options=explode(',',(string)$data1['1']);
        sort($selected_options);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($data1['2']);
        $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
        foreach ($_children  as $child) {
            $option_id=array(); 
            $attribute=$child->getAttributes();
              foreach($attribute as $value) {
                if (in_array($value->getAttributeCode(),$attribute_code)) {
                 
                 if ($value->usesSource()) {
                  
                   $option_id1 = $value->getSource()->getOptionId($value->getFrontend()->getValue($child));
                   array_push($option_id,$option_id1);
                  }
                }
            }
            sort($option_id);
            if ($selected_options == $option_id)
            {
              $response=$child->getId();
              break;            
            }
        }
            
        
        $result = $this->_resultJsonFactory->create();
        $result->setData(['output' => $response]);
        return $result;
        
    }
 
}