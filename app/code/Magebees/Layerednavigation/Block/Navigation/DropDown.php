<?php
namespace Magebees\Layerednavigation\Block\Navigation;

class DropDown extends AbstractRenderLayered
{
    protected $_template = 'Magebees_Layerednavigation::layer/dropdown.phtml';
    private $htmlPagerBlock;
    protected $request; 
    protected $layerattributeFactory; 
    protected $_objectManager; 

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
       
        parent::__construct($context);
        $this->request =$context->getRequest();
        $this->layerattributeFactory = $layerattributeFactory;
        $this->_objectManager = $objectManager;
    }
    public function setHtmlPagerBlock(\Magento\Theme\Block\Html\Pager $htmlPagerBlock)
    {
        $this->htmlPagerBlock = $htmlPagerBlock;

        return $this;
    }
    public function getResetSelectUrl($attr_code)
    {
        $activeFilters=$this->_objectManager->get('\Magento\LayeredNavigation\Block\Navigation\State')->getActiveFilters();
        foreach ($activeFilters as $_filter) {
            $request_var=$_filter->getFilter()->getRequestVar();
            if ($attr_code==$request_var) {
                $url=$this->_objectManager->get('\Magento\LayeredNavigation\Block\Navigation\State')->getRemoveUrl($_filter);
                return $url;
            }
        }
    }
        
        
    /**
     * @return array
     */
    public function getFilterItems()
    {
       /*fro here set sort order for options in layer navigation*/
        $items = $this->filter->getItems();
        if ($this->filter->hasAttributeModel()) {
            $attributeModel = $this->filter->getAttributeModel();
            $sort_order ='';
            $attr_id = $attributeModel->getId();
            $layer_model=$this->layerattributeFactory->create();
                $attributeCollection = $layer_model->getCollection()
                                    ->addFieldToFilter('attribute_id', $attr_id);
            
            foreach ($attributeCollection as $attr) {
                $sort_order =  $attr->getData('sort_option');
            }
        } else {
            $sort_order='';
        }
        
        if ($sort_order) {
            if ($sort_order == 1) {
                usort($items, [$this, "_sortByName"]);
            } elseif ($sort_order == 2) {
                usort($items, [$this, "_sortByCounts"]);
            }
        } else {
            return $items ;
        }
        return $items;
        //return $this->filter->getItems();
    }
    public function _sortByName($a, $b)
    {
        $x = trim($a->getLabel());
        $y = trim($b->getLabel());

        if ($x == '') {
            return 1;
        }
        if ($y == '') {
            return -1;
        }

        if (is_numeric($x) && is_numeric($y)) {
            if ($x == $y) {
                return 0;
            }
            return ($x < $y ? 1 : -1);
        } else {
            return strcasecmp($x, $y);
        }
    }
    public function _sortByCounts($a, $b)
    {
        
        if ($a->getCount() == $b->getCount()) {
            return 0;
        }
        
        return ($a->getCount() < $b->getCount() ? 1 : -1);
    }
    public function getApplyPrice()
    {
         $filter = $this->request->getParam($this->filter->getRequestVar());
         return $filter;
    }
}
