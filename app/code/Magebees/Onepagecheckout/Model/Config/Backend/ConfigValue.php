<?php
namespace Magebees\Onepagecheckout\Model\Config\Backend;
use Magento\Framework\App\Config\Value;

class ConfigValue extends Value
{
	protected $_magebeesHelper;

    public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\App\Config\ScopeConfigInterface $config,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
		\Magebees\Onepagecheckout\Helper\Data $magebeesHelper,
		?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
		) {

		parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
		
		$this->_magebeesHelper = $magebeesHelper;
	}

    public function resolveSerializedValue()
    {
    	if(is_array($this->getValue())){
    		
    	}else{
    		return $this->_magebeesHelper->mbunserialize($this->getValue()) !== false ? $this->_magebeesHelper->mbunserialize($this->getValue()) : $this->getValue();
    	}
    }
}