<?php
namespace Magebees\PocoBase\Block\System\Config;

use \Magento\Framework\App\ObjectManager;

class Edit extends \Magento\Config\Block\System\Config\Edit
{
    
    /**
     * Prepare layout object
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($this->getRequest()->getParam('section'));
        $this->_formBlockName = $section->getFrontendModel();
        if (empty($this->_formBlockName)) {
            $this->_formBlockName = self::DEFAULT_SECTION_BLOCK;
        }
        $this->setTitle($section->getLabel());
        $this->setHeaderCss($section->getHeaderCss());

        $objectManager = ObjectManager::getInstance();

        $authSession = $objectManager->create('Magento\Backend\Model\Auth\Session');
        $aclRetriever = $objectManager->create('Magento\Authorization\Model\Acl\AclRetriever');

        $user = $authSession->getUser();
        $role = $user->getRole();
        $resources = $aclRetriever->getAllowedResourcesByRole($role->getId());
        if($role->getRoleName()=="Pocodemo"){
            $this->getToolbar()->addChild(
            'save_poco',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'id' => 'save',
                'label' => __('Only Preview'),
                'class' => 'save primary'
                
            ]
            );
            
        }else{
           $this->getToolbar()->addChild(
            'save_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'id' => 'save',
                'label' => __('Save Config'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#config-edit-form']],
                ]
            ]
        );  
        }


        $block = $this->getLayout()->createBlock($this->_formBlockName);
        $this->setChild('form', $block);
        //return parent::_prepareLayout();
    }

   
}
