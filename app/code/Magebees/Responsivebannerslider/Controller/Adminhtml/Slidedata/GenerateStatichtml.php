<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidedata;

class GenerateStatichtml extends \Magento\Backend\App\Action
{
    protected $_coreSession;
	protected $_scopeConfig;
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
		\Magento\Framework\Filesystem\Driver\File $_file,
		\Magebees\Responsivebannerslider\Helper\Data $helper,
		\Magento\Framework\Json\Helper\Data $jsonhelper,
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
		\Magebees\Responsivebannerslider\Model\Slide $slide
	) {
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->_file = $_file;
		$this->_coreSession = $coreSession;
		$this->helper = $helper;
		$this->jsonhelper = $jsonhelper;
		$this->responsivebannerslider = $responsivebannerslider;
		$this->slide = $slide;
    }
    public function execute()
    {
		
		$params = $this->getRequest()->getParams();
        $file_list = json_decode($this->getRequest()->getParam('file_list'));
		
        try {
			if(isset($file_list[0])){
			$groupId = $file_list[0][0]; 
			$myFile = $file_list[0][1];
			
			$dir_path_array = explode("/",$myFile);
			array_pop($dir_path_array);
			$myFile_path =  implode("/",$dir_path_array);
				
			$dir_permission = 0755;
			$file_permission = 0644;
			
				if(!$this->_file->isExists($myFile_path))
				{
				$this->_file->createDirectory($myFile_path,$dir_permission);
				}
				
					
				if(!$this->_file->isWritable($myFile_path))
					{
					$this->_file->changePermissionsRecursively($myFile_path,$dir_permission,$file_permission);
					}
				
				
				
				
	
			$html_content = $this->generateStaticHtml($groupId);

			
			if (file_exists($myFile)) {
				unlink($myFile); // delete file	
			}
			$fh = fopen($myFile, 'w'); // or die("error");
			$search = [
				'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
				'/[^\S ]+\</s',  // strip whitespaces before tags, except space
				'/(\s)+/s'       // shorten multiple whitespace sequences
				];
			$replace = [ '>', '<','\\1'];
			$menu_compressedhtml = preg_replace($search, $replace, $html_content);
			fwrite($fh, $menu_compressedhtml);
			fclose($fh);
				unset($file_list[0]);
			}
			
			
			$info = array();
			if(count($file_list) > 0)
			{
			$info['next'] = true;
			}else{
			$info['next'] = false;
				$this->_coreSession->start();
				$publishhtml = $this->_coreSession->getPublishHtml();
				$developer_mode_enable_disable = $this->_scopeConfig->getValue('responsivebannerslider/optimize_performance/developer_mode_enable_disable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
					if(!$developer_mode_enable_disable){	
						
						$message = __('PUBLISH Static HTML Slider Successfully.');
                    	$this->messageManager->addSuccess($message);	
						
					}
				$this->_coreSession->start();
				$publishhtml = $this->_coreSession->unsPublishHtml();
			}
			$this->getResponse()->representJson($this->jsonhelper->jsonEncode($info));
            return;
			} catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
       		$info = array(); 
			$info['error_message'] = $e->getMessage();
			
			$this->getResponse()->representJson($this->jsonhelper->jsonEncode($info));
            return;
		
		}
	}
	public function generateStaticHtml($groupId)
	{
        $group_option = '';
        $group_details = $this->responsivebannerslider->load($groupId);
        //if ($group_details->getStatus() == "1") 
        {
          
        	$sliderhtml = $this->helper->generateStaticHtml($group_details->getData());
            return $sliderhtml;
        } 
    }
    protected function _isAllowed()
    {
        return true;
    }
}

