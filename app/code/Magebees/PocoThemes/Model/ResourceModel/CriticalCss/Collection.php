<?php
namespace Magebees\PocoThemes\Model\ResourceModel\CriticalCss;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\PocoThemes\Model\CriticalCss', 'Magebees\PocoThemes\Model\ResourceModel\CriticalCss');
    }
}
