<?php
namespace Magebees\Onepagecheckout\Model\Config\Backend\TrustSeals;
use Magebees\Onepagecheckout\Model\Config\Backend\TrustSeals\Badges\Validator;
use Magebees\Onepagecheckout\Model\Config\Backend\ConfigValue;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magebees\Onepagecheckout\Helper\Data;

class Badges extends ConfigValue
{
    private $validator;
    private $magebeesHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Data $magebeesHelper,
        Validator $validator,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
        ) {

        parent::__construct($context, $registry, $config, $cacheTypeList, $magebeesHelper, $resource, $resourceCollection, $data);
        $this->magebeesHelper = $magebeesHelper;
        $this->validator = $validator;

        }
    public function beforeSave()
    {
        $value = $this->resolveSerializedValue();
        unset($value['__empty']);
        if(!empty($value)){
            $value = array_values($value);
            $this->setValue($this->magebeesHelper->mbserialize($value));
        }
        return $this;
    }
    public function afterLoad()
    {
        if($this->getValue()){
            $value = $this->magebeesHelper->mbunserialize($this->getValue());
            if (is_array($value)) {
                $this->setValue($value);
            }
            return $this;
        }
    }
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}