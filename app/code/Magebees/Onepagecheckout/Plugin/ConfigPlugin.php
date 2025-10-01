<?php
namespace Magebees\Onepagecheckout\Plugin;
use Magebees\Onepagecheckout\Helper\Configurations;
use Magento\Framework\ObjectManagerInterface;
class ConfigPlugin
{
    protected $_helper;
    protected $_objectManager;
	
    public function __construct(
        Configurations $helper,
        ObjectManagerInterface $objectManager
    )
    {
        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
    }
    public function getEnable()
    {
        return $this->_helper->getEnable();
    }
    public function afterSave()
    {
        if(!$this->getEnable()){
            $outputPath = "advanced/modules_disable_output/Magebees_Onepagecheckout";
            $config = $this->_objectManager->create('Magento\Config\Model\ResourceModel\Config');
            $config->saveConfig($outputPath, 1, 'default', 0);
        }else{
            $outputPath = "advanced/modules_disable_output/Magebees_Onepagecheckout";
            $config = $this->_objectManager->create('Magento\Config\Model\ResourceModel\Config');
            $config->saveConfig($outputPath, 0, 'default', 0);
        }
    }
}
