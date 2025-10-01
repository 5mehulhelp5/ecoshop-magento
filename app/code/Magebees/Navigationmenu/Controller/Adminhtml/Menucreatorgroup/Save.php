<?php
namespace Magebees\Navigationmenu\Controller\Adminhtml\Menucreatorgroup;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{
	protected $_collection;
	protected $_scopeConfig;
	protected $_file;
	protected $menucreatorgroup;
	protected $reader;
	protected $session;
	protected $helper;
	protected $CriticalCss;
	public function __construct( \Magento\Backend\App\Action\Context $context,
	\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
	\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
	\Magento\Framework\Filesystem\Driver\File $_file,
	\Magebees\Navigationmenu\Model\Menucreatorgroup $menucreatorgroup,	
	\Magento\Framework\Module\Dir\Reader $reader,
	\Magento\Backend\Model\Session $session,
	\Magebees\Navigationmenu\Helper\Data $helper,	\Magebees\PocoThemes\Model\CriticalCss $CriticalCss,	
	array $data = [])
	{
		$this->_collection = $collection;
		$this->_scopeConfig = $_scopeConfig;
		$this->_file = $_file;
		$this->menucreatorgroup = $menucreatorgroup;
		$this->reader = $reader;
		$this->session = $session;
		$this->helper = $helper;		$this->CriticalCss = $CriticalCss;		
		parent::__construct($context);
	}
	
	
	public function execute()
	    {		
		$currentDate = date('Y-m-d h:i:s');	
       	$data=$this->getRequest()->getPost()->toarray();
		if($data)
		{
			$id = $this->getRequest()->getParam('id');
		    if ($id)
			{
                $this->menucreatorgroup->load($id);
				 if ($id != $this->menucreatorgroup->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
            }
			/* Set blank value for the dependent field*/
			if(!isset($data['root']['lvl0dvcolor'])){
			$data['root']['lvl0dvcolor']='';	
			}
			if(!isset($data['sub']['sublvl1dvcolor'])){
			$data['sub']['sublvl1dvcolor']='';	
			}
			if(!isset($data['mega']['mmlvl1dvcolor'])){
			$data['mega']['mmlvl1dvcolor']='';	
			}
			if(!isset($data['mega']['mmlvl2dvcolor'])){
			$data['mega']['mmlvl2dvcolor']='';	
			}
			if(!isset($data['mega']['mmlvl3dvcolor'])){
			$data['mega']['mmlvl3dvcolor']='';	
			}
			if(!isset($data['fly']['ddlinkdvcolor'])){
			$data['fly']['ddlinkdvcolor']='';	
			}
			$data['rootoptions'] = json_encode($data['root']);
			$data['megaoptions'] = json_encode($data['mega']);
			$data['suboptions'] = json_encode($data['sub']);
			$data['flyoptions'] = json_encode($data['fly']);
			
			
			$unique_code = preg_replace('!\s+!', ' ', $data['title']); 
			// Replace Multiple Space
			$unique_code = str_replace(' ', '-', strtolower($unique_code));
			// Replaces all spaces with hyphens.			//
			$unique_code = preg_replace('/[^A-Za-z0-9\-]/', '', $unique_code); // Removes special chars.
			
			$data['unique_code'] = $unique_code;
			$this->menucreatorgroup->setData($data);
			try {
					if ($this->menucreatorgroup->getCreatedTime() == NULL || $this->menucreatorgroup->getUpdateTime() == NULL) 
					{
						$this->menucreatorgroup->setCreatedTime($currentDate)
								->setUpdateTime($currentDate);
					} 
					else 
					{
						$this->menucreatorgroup->setUpdateTime($currentDate);
					}	
					$this->menucreatorgroup->setDescription("This is descriptions");
					if(isset($data['alignment']))
					{
					$this->menucreatorgroup->setAlignment($data['alignment']);
					}
					$this->menucreatorgroup->save();
					
					/*Start Working to  Create & Update the Dynamic Css file for the menu items*/
					if($this->getRequest()->getParam('id') == "")
					{
					$group_id = $this->menucreatorgroup->getData('group_id');
					$menu_type = $this->menucreatorgroup->getData('menutype');
					}
					else
					{
					$group_id = $this->getRequest()->getParam('id');
					}
					if(isset($data['unique_code']))
					{
					$unique_code = $data['unique_code'].'-'.$group_id;
					$this->menucreatorgroup->setUniqueCode($unique_code);
					$this->menucreatorgroup->save();
					}
					$menu_type = $this->menucreatorgroup->getData('menutype');
					$moduleviewDir=$this->reader->getModuleDir('view', 'Magebees_Navigationmenu');
					$cssDir=$moduleviewDir.'/frontend/web/css/navigationmenu';
				
					$dir_permission = 0755;
					$file_permission = 0644;
					$path_less = $cssDir.'/';									
					
					$dir_for_dynamic_file = 'pub/media/magebees/navigationmenu';											
					$path_css = $this->helper->getDynamicCSSDirectoryPath($dir_for_dynamic_file);
					if(!$this->_file->isExists($path_css))
					{
						$this->_file->createDirectory($path_css,$dir_permission);
					}
					if(!$this->_file->isWritable($path_css))
					{
					$this->_file->changePermissionsRecursively($path_css,$dir_permission,$file_permission);
					}
				
					if($this->menucreatorgroup->getMenutype()){
						$css_less = $this->get_less_variable($group_id);
						$menu_type = $this->menucreatorgroup->getMenutype();
						//$oldfile = "-".$group_id.".less";
						$files1 = scandir($path_less);
						$list_lessfiles =  $cssDir.'/*.less';
						$file_name_check = "-".$group_id.".less";
						foreach (glob($list_lessfiles) as $filename) {
							if($this->endsWith($filename, $file_name_check)){
								if(is_file($filename)) {
								$result = unlink($filename); // delete Old Less file
								}
							}
						}
						$path_less .= $menu_type."-".$group_id.".less";
						$path_css .= $menu_type."-".$group_id.".css";
						if($menu_type=="mega-menu")
						{
								$master_less_file = $cssDir.'/'.'master-mega-menu.php';
								$master_less = file_get_contents($master_less_file);
								$content = $css_less.$master_less;
							
								$parser = new \Less_Parser(['relativeUrls' => false,'compress' => true]);
								 gc_disable();
            					$parser->parse($content);
            					$group_dynamic_css_content = $parser->getCss();
            					gc_enable();
								file_put_contents($path_css,$group_dynamic_css_content);								$criticalCss = $this->CriticalCss->getCollection()								 ->addFieldToFilter('stores', $unique_code)								 ->addFieldToFilter('type', 'menu_group');								 if($criticalCss->getSize()>0):									 $criticalCss->walk('delete');								 endif;								 $criticalCssInfo = array();								 $criticalCssInfo['stores'] = $unique_code;								 $criticalCssInfo['type'] = 'menu_group';								 $criticalCssInfo['css'] = $group_dynamic_css_content;								 $this->CriticalCss->setData($criticalCssInfo)->save();
					
						}elseif(($menu_type=="smart-expand")||($menu_type=="always-expand")){
								$master_less_file = $cssDir.'/'.'master-expand-menu.php';
								$master_less = file_get_contents($master_less_file);
								$content = $css_less.$master_less;
								$parser = new \Less_Parser(['relativeUrls' => false,'compress' => true]);
								 gc_disable();
            					$parser->parse($content);
            					$group_dynamic_css_content = $parser->getCss();
            					gc_enable();
								file_put_contents($path_css,$group_dynamic_css_content);								$criticalCss = $this->CriticalCss->getCollection()								 ->addFieldToFilter('stores', $unique_code)								 ->addFieldToFilter('type', 'menu_group');								 if($criticalCss->getSize()>0):									 $criticalCss->walk('delete');								 endif;								 $criticalCssInfo = array();								 $criticalCssInfo['stores'] = $unique_code;								 $criticalCssInfo['type'] = 'menu_group';								 $criticalCssInfo['css'] = $group_dynamic_css_content;								 $this->CriticalCss->setData($criticalCssInfo)->save();
						}
					}
					/*End Working to  Create & Update the Dynamic Css file for the menu items*/
				
					$this->messageManager->addSuccess(__('The Record has been saved.'));
					$this->session->setFormData(false);
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $this->menucreatorgroup->getId(), '_current' => true));
						return;
					}
					$this->_redirect('*/*/');
					return;
            	}
				catch (\Magento\Framework\Model\Exception $e) 
				{
                	$this->messageManager->addError($e->getMessage());
            	}
				catch (\RuntimeException $e) 
				{
                	$this->messageManager->addError($e->getMessage());
            	} 
				catch (\Exception $e)
				{
					$this->messageManager->addError($e->getMessage());
                	
            	}
				$this->_getSession()->setFormData($data);
				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$resultRedirect->setUrl($this->_redirect->getRefererUrl());
				return $resultRedirect;
				
		}
		$this->_redirect('*/*/');
    }
    
	public function get_less_variable($group_id)
	{
		$groupdata = $this->menucreatorgroup->load($group_id);
		$scope_info = $this->_scopeConfig->getValue('navigationmenu/general',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$responsive_breakpoint = '';
		$dynamic_variable = '';
		$dynamic_variable = '@group_id:'.$group_id.';'.PHP_EOL;
		$itemimageheight = $groupdata->getImageHeight().'px';
		$dynamic_variable .= '@itemimageheight:'.$itemimageheight.';'.PHP_EOL;
		$itemimagewidth = $groupdata->getImageWidth().'px';
		$dynamic_variable .= '@itemimagewidth:'.$itemimagewidth.';'.PHP_EOL;
		
		if(isset($scope_info['responsive_break_point']))
		{
		$dynamic_variable .= '@responsive_breakpoint:'.$scope_info['responsive_break_point'].';'.PHP_EOL;	
		}else{
			$dynamic_variable .= '@responsive_breakpoint:767px;'.PHP_EOL;
		}
		
		
		$informations = $groupdata->getData();
			foreach($informations as $key => $value):
					if($this->isJSON($value)){
					$sub_information = json_decode($value, true);
					foreach($sub_information as $subkey => $subvalue):
						if($subvalue==""){
							
							$textColor = array('titlecolor','lvl0color','lvl0colorh','lvl0colora','mmlvl1color','mmlvl1colorh','mmlvl1colora','mmlvl2color','mmlvl2colorh', 'mmlvl2colora','mmlvl3color','mmlvl3colorh','mmlvl3colora','sublvl1color','sublvl1colorh','sublvl1colora','sublvl2color','sublvl2colorh', 'sublvl2colora','sublvl3color','sublvl3colorh','sublvl3colora','ddlinkcolor','ddlinkcolorh','ddlinkcolora');
							
							$bgColor = array('titlebgcolor','menubgcolor','lvl0bgcolor','lvl0bgcolorh','lvl0bgcolora','mmpnl-bgcolor','mmlvl1bgcolor','mmlvl1bgcolorh','mmlvl1bgcolora','mmlvl2bgcolor','mmlvl2bgcolorh','mmlvl2bgcolora','mmlvl3bgcolor','mmlvl3bgcolorh','mmlvl3bgcolora','sublvl1bgcolor','sublvl1bgcolorh','sublvl1bgcolora','sublvl2bgcolor','sublvl2bgcolorh','sublvl2bgcolora','sublvl3bgcolor','sublvl3bgcolorh','sublvl3bgcolora', 'ddpnl-bgcolor', 'ddlinkbgcolor','ddlinkbgcolorh','ddlinkbgcolora');
							
							$dividerColor = array('lvl0dvcolor','mmpnl-bdcolor','mmlvl1dvcolor','mmlvl2dvcolor','mmlvl3dvcolor','sublvl1dvcolor','sublvl2dvcolor','sublvl3dvcolor','ddpnl-bdcolor','ddlinkdvcolor');
							
							if (in_array($subkey, $textColor))
							{
								$subvalue = '#111111';
								$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
								continue;
							}else if (in_array($subkey, $bgColor)){
								$subvalue = 'transparent';
								$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
								continue;
							}else if (in_array($subkey, $dividerColor)){
								$subvalue = '#E1E1E1';
								$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
								continue;
							}
							$dynamic_variable .= '@'.$subkey.':;'.PHP_EOL;	
						}else{
							$dynamic_variable .= '@'.$subkey.':'.$subvalue.';'.PHP_EOL;
						}
					endforeach;
			}
			endforeach;
		return $dynamic_variable;
		
		
		
	}

	protected function _isAllowed()
    {
		return true;
        
    }
	public function isJSON($string){
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}
}
