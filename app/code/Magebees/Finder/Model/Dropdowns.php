<?php
namespace Magebees\Finder\Model;

class Dropdowns extends \Magento\Framework\Model\AbstractModel
{
    
    protected $_options = [];
    protected $_parent_ids = [];
    protected $_ymm_parent_ids = [];
    protected $_finderFactory;
    protected $_ymmvalueFactory;
    
    
    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Finder\Model\ResourceModel\Dropdowns');
    }
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magebees\Finder\Model\FinderFactory $finderFactory,
        \Magebees\Finder\Model\YmmvalueFactory $ymmvalueFactory,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_finderFactory = $finderFactory;
        $this->_ymmvalueFactory = $ymmvalueFactory;
        
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
        
    public function getValues($parentId, $dropdownId, $cat_id = 0)
    {
                
        $finder =  $this->_finderFactory->create()->load($this->getFinderId());
        
        $dropdowns = $finder->getDropdowns($this->getFinderId())->getFirstItem();
        $options = array();
        $options[] = [
            'value'    => 0,
            'label'    => __($this->getName()),
            'selected' => false,
        ];

        if (($dropdowns->getId()!=$this->getId()) && (!$parentId) && (!$dropdownId)) {
            return $options;
        }
        
        $collection = $this->_ymmvalueFactory->create()->getCollection()->addFieldToFilter('parent_id', $parentId);
        $collection->addFieldToFilter('dropdown_id', $this->getId());
                           
        if ($this->getSort()=='asc') {
            $order = 'value ASC';
        } else {
            $order = 'value DESC';
        }
       
        $collection->getSelect()->order($order);
        
        foreach ($collection as $option) {
            $options[] = [
                'value'    => $option->getYmmValueId(),
                'label'    => __($option->getValue()),
                'selected' => ($dropdownId == $option->getYmmValueId()),
            ];
        }
        return $options;
    }
}
