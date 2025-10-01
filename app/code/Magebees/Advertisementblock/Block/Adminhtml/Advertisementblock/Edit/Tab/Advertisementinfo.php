<?php

namespace  Magebees\Advertisementblock\Block\Adminhtml\Advertisementblock\Edit\Tab;

class Advertisementinfo extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;
	protected $helper;
	protected $advertisement_images;


   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magebees\Advertisementblock\Helper\Data $helper,
        \Magebees\Advertisementblock\Model\Advertisementimages $advertisement_images,
        array $data = []
    ) {
        $this->setTemplate('advertisement_form.phtml');
        $this->_systemStore = $systemStore;
        $this->helper = $helper;
        $this->advertisement_images = $advertisement_images;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getEditFormData()
    {
        $model = $this->_coreRegistry->registry('advertisement_block')->getData();
        return $model;
    }
    public function getEditFieldInfo($advertisement_id)
    {
        $adv_img_coll = $this->advertisement_images->getCollection();
        $adv_img_coll->addFieldToFilter('advertisement_id', $advertisement_id);
         $data=$adv_img_coll->getData();
        return $data;
    }
  
    public function getTabLabel()
    {
        return __('Advertisement Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Advertisement Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
