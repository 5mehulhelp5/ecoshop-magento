<?php
namespace Magebees\Layerednavigation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class SaveConfig implements ObserverInterface
{

	
	protected $_scopeConfig;
	protected $_cacheTypeList;
    protected $_cacheFrontendPool;
    protected $attributeoptionFactory;
    protected $resourceConnection;
    protected $httpRequest;
    protected $dirReader;
    protected $_storeManager;   

    public function __construct(
       \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
    		\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
		  \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,
        \Magento\Framework\App\Request\Http $httpRequest,      
		 \Magento\Store\Model\StoreManagerInterface $storeManager,
		  \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		 \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Module\Dir\Reader $dirReader
    ) {
      $this->attributeoptionFactory = $attributeoptionFactory;
        $this->_cacheTypeList = $cacheTypeList;
		$this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resourceConnection = $resourceConnection;
        $this->httpRequest = $httpRequest;
        $this->dirReader = $dirReader;
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
		
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		
		try
		{
		
		 $postdata=$this->httpRequest->getPost()->toArray();
			//print_r($postdata);die;
		  $option_model=$this->attributeoptionFactory->create();
          $opt_collection = $option_model->getCollection();
			if(isset($postdata['groups']['seo_setting']['fields']['replace_char']['value']))
			{
			$replace_config_val=$postdata['groups']['seo_setting']['fields']['replace_char']['value'];
			}
			else
			{
				$replace_config_val=$postdata['groups']['seo_setting']['fields']['replace_char']['inherit'];
			}
			
			if(isset($postdata['groups']['seo_setting']['fields']['replace_char_all']['value']))
			{
			$replaceall_config_val=$postdata['groups']['seo_setting']['fields']['replace_char_all']['value'];
			}
			else
			{				
				$replaceall_config_val=$postdata['groups']['seo_setting']['fields']['replace_char_all']['inherit'];
			}
			if($replace_config_val==0)
			{
				$replaceChar='-';
			}
			else
			{
				$replaceChar='_';
			}
			foreach($opt_collection as $opt)
			{
				 $option_model=$this->attributeoptionFactory->create();
				$option_id=$opt->getId();
				$option_model->load($option_id);
				$option_alias=$opt->getUrlAlias();	
				$main_option_alias=$opt->getMainUrlAlias();	
				if($replaceall_config_val==0)
				{
					//$option_alias = preg_replace('/[^a-zA-Z0-9]/s',$replaceChar, $main_option_alias);
					$option_alias = preg_replace('/[^\w\s]+/u',$replaceChar, $main_option_alias);
                $option_alias =preg_replace('/_+/',$replaceChar, $option_alias);
					$main_option_alias=$option_alias;
				}
				$option_alias = str_replace(' ',$replaceChar, $main_option_alias);                
				//$option_alias = str_replace('/',$replaceChar, $option_alias);     
				$option_alias = preg_replace('/'.$replaceChar.'+/',$replaceChar, $option_alias);	
				$option_model->setUrlAlias($option_alias);
				$option_model->save();
			}
		
			
		$types = array('magebees_layerednavigation');
			foreach ($types as $type) {
				$this->_cacheTypeList->cleanType($type);
			}
			foreach ($this->_cacheFrontendPool as $cacheFrontend) {
				$cacheFrontend->getBackend()->clean();
			}
		}
		catch (\Exception $e) {
		{
			//print_r($e->getMessage());die;
		}
    }
	
}
}
