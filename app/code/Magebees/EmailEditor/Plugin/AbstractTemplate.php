<?php
namespace Magebees\EmailEditor\Plugin;

class AbstractTemplate
{   
    protected $helper;
 	public function __construct(       
        \Magebees\EmailEditor\Helper\Data $helper
    ) {
            $this->helper=$helper;
    }   
    public function aroundGetDefaultEmailLogo(
        \Magento\Email\Model\AbstractTemplate $subject,
        \Closure $proceed
    ) {
     
      $is_ext_enabled=$this->helper->getConfig('emaileditor/setting/enable');
        if($is_ext_enabled)
        {
            $light_logo=$this->helper->getConfig('emaileditor/light_logo/image');
	        $image=$this->helper->getLightLogoImageUrl();
	    	if($light_logo)
	    	{
	    		return $image;
	    	}    	 
    	}  
		$result = $proceed();   
		return $result;    
               
    }  
    public function aroundGetLogoUrl(
        \Magento\Email\Model\AbstractTemplate $subject,
        \Closure $proceed,
        $store
    ) {
    	$is_ext_enabled=$this->helper->getConfig('emaileditor/setting/enable');
        if($is_ext_enabled)
        {
	    	$image=$this->helper->getLightLogoImageUrl();
	    	if($image)
	    	{
	    		return $image;
	    	}
    	}
    	
		 $result = $proceed($store);
		 return $result;
    	                
             
    }
    public function aroundGetLogoAlt(
        \Magento\Email\Model\AbstractTemplate $subject,
        \Closure $proceed,
        $store
    ) {
    	$is_ext_enabled=$this->helper->getConfig('emaileditor/setting/enable');
        if($is_ext_enabled)
        {
    	$alt_text=$this->helper->getConfig('emaileditor/light_logo/alt_text');
    	if($alt_text)
    	{
    		return $alt_text;
    	}
    	}
		 $result = $proceed($store);
		 return $result;
    	                
             
    }
    public function aroundAddEmailVariables(
        \Magento\Email\Model\AbstractTemplate $subject,
        \Closure $proceed,
        $variables, $storeId
    ) {
		$result = $proceed($variables, $storeId);
		$is_ext_enabled=$this->helper->getConfig('emaileditor/setting/enable');
        if($is_ext_enabled)
        {
    	
    	$width=$this->helper->getConfig('emaileditor/light_logo/width');
		$height=$this->helper->getConfig('emaileditor/light_logo/height');
		if($width)
		{
			$result['logo_width']=$width;
		}
		if($height)
		{
			$result['logo_height']=$height;
		}		
		}
    	return $result;
    	
    }    
}