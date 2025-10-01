<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slide\Renderer;
use Magento\Framework\DataObject;
use \Magebees\Responsivebannerslider\Model\Responsivebannerslider ;
class Groups extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
   
   private $_responsivebannerslider;
  
    public function __construct(\Magento\Backend\Block\Context $context, Responsivebannerslider $responsivebannerslider, array $data = [])
    {
        $this->_responsivebannerslider = $responsivebannerslider;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
	
   public function render(DataObject $row)
   {
		$value =  $row->getData($this->getColumn()->getIndex());
		$groupdata = explode(",",$value);
		$Slider_Groups = '';
		for($i=0; $i<count($groupdata); $i++) {
			$groups = $this->_responsivebannerslider->load($groupdata[$i]);
			$title = $groups->getData('title');
			if($i ==0){
				$Slider_Groups = $title; 		 
			}else{
				$Slider_Groups .= ", ".$title; 		 
			}
		}
		return $Slider_Groups;
   }
}
