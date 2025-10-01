<?php

namespace  Magebees\CacheWarmer\Block\Adminhtml\Warmerreport\Edit\Tab;

class PageReport extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;

   protected $_template = 'page_report.phtml';
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore, 
		\Magebees\CacheWarmer\Model\LogFactory $warmerLogFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,		
        array $data = []
    ) {
        //$this->setTemplate('page_report.phtml');
        $this->_systemStore = $systemStore;     
		$this->warmerLogFactory = $warmerLogFactory;	
		$this->timezone = $timezone;		
        parent::__construct($context, $registry, $formFactory, $data);
    }

  
  
    public function getTabLabel()
    {
        return __('Warmed Pages');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Warmed Pages');
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
	
	public function dateFormat($date)
    {
        return $this->timezone->date($date)->format('Y-m-d');
    }
    public function dateTextFormat($date)
    {
        
        return $this->timezone->formatDate(
     $date,
    \IntlDateFormatter::LONG,
    false,
    true
);
    }
}
