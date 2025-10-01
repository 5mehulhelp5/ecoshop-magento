<?php
namespace Magebees\Finder\Model;

class Finder extends \Magento\Framework\Model\AbstractModel
{
    protected $_dropdownsFactory;
    protected $_ymmvalueFactory;
    protected $_mapvalueFactory;
    protected $_catalogSession;
	/**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Finder\Model\ResourceModel\Finder');
    }

    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magebees\Finder\Model\DropdownsFactory $dropdownsFactory,
        \Magebees\Finder\Model\YmmvalueFactory $ymmvalueFactory,
        \Magebees\Finder\Model\MapvalueFactory $mapvalueFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_dropdownsFactory = $dropdownsFactory;
        $this->_ymmvalueFactory = $ymmvalueFactory;
        $this->_mapvalueFactory = $mapvalueFactory;
        $this->_catalogSession = $catalogSession;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    
    public function getDropdowns($finderId)
    {
        $collection = $this->_dropdownsFactory->create()->getCollection()
            ->addFieldToFilter('finder_id', $finderId);
        return $collection;
    }
    
    public function isDeleteYmmData($ymm_value_id)
    {
        $mapCollection = $this->_mapvalueFactory->create()->getCollection()->addFieldToFilter('ymm_value_id', $ymm_value_id);
        $count =  $mapCollection->getSize();
        
        if ($count) {
            return false;
        }
    
        $ymmCollection = $this->_ymmvalueFactory->create()->getCollection()->addFieldToFilter('parent_id', $ymm_value_id);
        
        $mcount =  $ymmCollection->getSize();
        if ($mcount) {
            return false;
        }
        return true;
    }
    
    
    public function saveDropDownValues($dropdowns)
    {
        $session = $this->_catalogSession;
        $findername    = 'mbfinder_' . $this->getId();

        if (!$dropdowns) {
            return false;
        }
            
        if (!is_array($dropdowns)) {
            return false;
        }
         
        $values = [];
        $id      = 0;
        $current = 0;
        foreach ($this->getDropdowns($this->getId()) as $d) {
            $id = $d->getId();
            $values[$id] = isset($dropdowns[$id]) ? $dropdowns[$id] : 0;
            if (isset($dropdowns[$id]) && ($dropdowns[$id])) {
                $current = $dropdowns[$id];
            }
        }
        
        if ($id) {
            $values['last']    = $values[$id];
            $values['current'] = $current;
        }
                   
        $session->setData($findername, $values);
        
        return true;
    }
    
    public function getSavedValue($dropdownId)
    {
        $session = $this->_catalogSession;
        $name    = 'mbfinder_' . $this->getId();
                
        $values = $session->getData($name);
        
        if (!is_array($values)) {
            return 0;
        }
            
        if (empty($values[$dropdownId])) {
            return 0;
        }
            
        return $values[$dropdownId];
    }
    
    public function getYmmValueFromPath($param)
    {
        //for compatible with layered navigation
        $res ="";
        $param = trim($param, "/");
        $ymm_url = explode("/", $param);
        if (array_key_exists(2, $ymm_url)) {
            $ymm_param = $ymm_url[2];
            $pos = strpos($ymm_param, '?');
            if ($pos) {
                $ymm_param = substr($ymm_param, 0, $pos);
            }
            $sep = "-";
            $ymm_param = explode($sep, $ymm_param);
            
            $res = $ymm_param[count($ymm_param)-1];
        }
        
        return $res;
    }
    
    public function getDropdownsByCurrent($current)
    {
        $dropdowns = [];
        while ($current) {
            $valueModel = $this->_ymmvalueFactory->create()->load($current);
            $dropdowns[$valueModel->getDropdownId()]= $current;
            $current = $valueModel->getParentId();
        }
        return $dropdowns;
    }
}
