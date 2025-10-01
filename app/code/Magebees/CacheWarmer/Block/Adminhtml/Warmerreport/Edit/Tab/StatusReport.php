<?php

namespace  Magebees\CacheWarmer\Block\Adminhtml\Warmerreport\Edit\Tab;

class StatusReport extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;

     //protected $_template = 'status_report.phtml';
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore, 
		\Magebees\CacheWarmer\Model\LogFactory $warmerLogFactory,
		\Magebees\CacheWarmer\Helper\Http $httpHelper,
        array $data = []
    ) {
        $this->setTemplate('status_report.phtml');
        $this->_systemStore = $systemStore;      
		$this->warmerLogFactory = $warmerLogFactory;	
		$this->httpHelper = $httpHelper;		
        parent::__construct($context, $registry, $formFactory, $data);
    }

  
  
    public function getTabLabel()
    {
        return __('Warmed Pages Status');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Warmed Pages Status');
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
