<?php
namespace Magebees\Finder\Model\ResourceModel\Mapvalue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Finder\Model\Mapvalue', 'Magebees\Finder\Model\ResourceModel\Mapvalue');
    }
    
    public function joinFields($finder)
    {
        $select = $this->getSelect();
        $select->reset(\Magento\Framework\DB\Select::FROM);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        
        $i=0;
        foreach ($finder->getDropdowns($finder->getId()) as $d) {
            $i = $d->getId();
            $table  = ["d$i" => $this->getTable('magebees_finder_ymm_value')];
            $fields = ["value$i" => "d$i.value","valueid$i" => "d$i.ymm_value_id"];
            $bind = "d$i.parent_id = d".($i-1).".ymm_value_id";
            $select->joinInner($table, $bind, $fields);
        }
        
        $select->joinInner(
            ['m'=>$this->getTable('magebees_finder_map_value')],
            "d$i.ymm_value_id = m.ymm_value_id",
            ['sku', 'val'=> 'm.ymm_value_id', 'vid'=>'m.map_value_id' ]
        );
        if ($i!=0) {
            $select->joinInner(
                ['n'=>$this->getTable('magebees_finder_dropdowns')],
                "d$i.dropdown_id = n.dropdown_id",
                ['finderid'=>'n.finder_id' ]
            )->where('n.finder_id='.$finder->getId());
        }
        return $this;
    }
}
