<?php
namespace Magebees\Finder\Controller\Index;

use \Magento\Framework\App\Action\Action;

class Finder extends Action
{
    protected $_dropdownsFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebees\Finder\Model\DropdownsFactory $dropdownsFactory
    ) {
        parent::__construct($context);
        $this->_dropdownsFactory = $dropdownsFactory;
    }
    
        
    public function execute()
    {
        $parentId   = $this->getRequest()->getParam('parent_id');
        $dropdownId =$this->getRequest()->getParam('dropdown_id');
        $cat_id =$this->getRequest()->getParam('cat_id');
        
        $options    = [];
        
        if ($parentId && $dropdownId) {
            $dropdown = $this->_dropdownsFactory->create()->load($dropdownId);
            $options  = $dropdown->getValues($parentId, $dropdownId, $cat_id);
        }
        
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($options)
        );
    }
}
