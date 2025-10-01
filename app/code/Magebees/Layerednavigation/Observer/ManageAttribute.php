<?php
namespace Magebees\Layerednavigation\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

class ManageAttribute implements ObserverInterface 
{     
	protected $productAttributeCollectionFactory;
    protected $attributeoptionFactory;
    protected $_scopeConfig;
    protected $attr_helper;
    protected $helper; 
    protected $cache_type;
     protected $layerattributeFactory;

	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory$productAttributeCollectionFactory,
		 \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
		 \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,
		 \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		 \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
		 \Magebees\Layerednavigation\Helper\Data $helper,
		 CacheTypeListInterface $cache_type
	)
	{
		$this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
		$this->layerattributeFactory = $layerattributeFactory;
		$this->attributeoptionFactory = $attributeoptionFactory;
		$this->_scopeConfig=$scopeConfig;
		$this->attr_helper = $attr_helper;
		$this->helper = $helper;
		$this->cache_type = $cache_type;
	} 
	public function execute(\Magento\Framework\Event\Observer $observer)
    {	
	$is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	$is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($is_default_enabled==0){
			if($is_enabled)	{	
				 $productAttributes = $this->productAttributeCollectionFactory->create();
				 $productAttributes->addFieldToFilter('is_filterable', array('gt' => 0))
				->addFieldToFilter('is_visible','1')
				->addFieldToFilter('is_user_defined','1');
				$attribute_collection=$productAttributes->getData();

				foreach($attribute_collection as $attr_col)
				{
					/**Start Manage newly added attribute and add in magebees_layernav_attribute table if not exists*/

					$layer_model=$this->layerattributeFactory->create();
					$collection = $layer_model->getCollection()
										->addFieldToFilter('attribute_id',$attr_col['attribute_id']);			
					if(!$collection->getSize()){
						$data = [
						'attribute_id' => $attr_col['attribute_id'],
						'display_mode'=>'1',
						'show_product_count'=>'1',
						'show_searchbox'=>'0',
						'unfold_option'=>'4',
						'always_expand'=>'0'

						];
						$layer_model->setData($data)->save();
					}
					/**End Manage newly added attribute and add in magebees_layernav_attribute table if not exists*/
					
					/**Start Manage newly added attribute option and add in magebees_layernav_attribute_option table if not exists*/
					$attr_code=$attr_col['attribute_code'];
					$options = $this->attr_helper->getAllOptions($attr_code);		

						foreach($options as $o){
							$option_model=$this->attributeoptionFactory->create();
							$opt_collection = $option_model->getCollection()
											->addFieldToFilter('attribute_id',$attr_col['attribute_id'])
											->addFieldToFilter('option_id',$o['value']);
							$main_url_alias=trim(strtolower($o['label']));					
							if(empty($opt_collection->getData())){						
								$url_alias=$this->helper->urlAliasAfterReplaceChar($o['label']);
								$option_data = [
								'attribute_id' =>$attr_col['attribute_id'],
								'option_id'=>$o['value'],
								'url_alias'=>$url_alias,
								'main_url_alias'=>$main_url_alias,
								'option_label'=>$o['label'] 						
								];
								$option_model->setData($option_data)->save();
							}else{
								
								$postdata=$observer->getRequest()->getPost()->toArray();
								if(isset($postdata['attribute_id']))	{
									if($postdata['attribute_id']==$attr_col['attribute_id']){
										$opt_arr=array_column($options,'value');
										$opt_custom_coll =$option_model->getCollection()->addFieldToFilter('attribute_id',$attr_col['attribute_id']);
										$opt_custom_data=$opt_custom_coll->getData();
										$all_opt_arr=array_column($opt_custom_data,'option_id');
										$option_diff=array_diff($all_opt_arr,$opt_arr);
										foreach($option_diff as $diff)	{
											$option_model->load($diff,'option_id');
											$option_model->delete();
										}	
									}
								}				
													
							} 
						}	

					
					/**End Manage newly added attribute option and add in magebees_layernav_attribute_option table if not exists*/			
				}
				
				$this->cache_type->invalidate('magebees_layerednavigation');		
			}
		}
		
    } 
}
