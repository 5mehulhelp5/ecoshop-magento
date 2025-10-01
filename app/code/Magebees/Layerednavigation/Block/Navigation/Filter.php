<?php
namespace Magebees\Layerednavigation\Block\Navigation;

class Filter extends AbstractRenderLayered
{

    protected $_template = 'Magebees_Layerednavigation::layer/filter.phtml';
    private $htmlPagerBlock;
    private $request;
    private $layerattributeFactory;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory
    ) {
        parent::__construct($context);
        $this->request =$context->getRequest();
        $this->layerattributeFactory = $layerattributeFactory;
    }

    public function setHtmlPagerBlock(\Magento\Theme\Block\Html\Pager $htmlPagerBlock)
    {
        $this->htmlPagerBlock = $htmlPagerBlock;
        return $this;
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
