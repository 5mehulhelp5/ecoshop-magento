<?php
namespace Magebees\Layerednavigation\Block\Navigation;

class FromTo extends AbstractRenderLayered
{
   
    protected $_template = 'Magebees_Layerednavigation::layer/fromto.phtml';
    protected $request; 
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
    
        parent::__construct($context);
        $this->request =$context->getRequest();
    }
    public function getFilterItems()
    {
        return $this->filter->getItems();
    }
     
    public function getFilterUrl()
    {
            
        if (strpos($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]), 'price') !== false) {
            $query = [$this->filter->getRequestVar() => $this->filter->getResetValue()];
            return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        } else {
             return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        }
    }
  
    public function getRemoveUrl()
    {
        $query = [$this->filter->getRequestVar() => $this->filter->getResetValue()];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
    public function getApplyPrice()
    {
         $filter = $this->request->getParam($this->filter->getRequestVar());
         return $filter;
    }
}
