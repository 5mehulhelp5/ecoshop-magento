<?php
namespace Magebees\Layerednavigation\Controller\Adminhtml\Manage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;

class Saveoption extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Layerednavigation\Model\AttributeoptionFactory $attributeoptionFactory,	
		 \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Eav\Model\Config $attrconfig,        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        CacheManager $cacheManager,
        CacheTypeListInterface $cache_type
    ) {
    
        $this->attributeoptionFactory = $attributeoptionFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->attrconfig = $attrconfig;
        $this->cache_type = $cache_type;
         $this->cacheManager = $cacheManager;
		  $this->_scopeConfig = $scopeConfig;
    
        parent::__construct($context);
    }
    public function execute()
    {
        $session =$this->_objectManager->get('Magento\Backend\Model\Session');
        $jsHelper = $this->_objectManager->get('Magento\Backend\Helper\Js');
        $storeId=$session->getTestKey();
        $optionId=$session->getOptionId();
        $attr_id=$session->getAttributeId();
        $data=$this->getRequest()->getPost()->toArray();
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
        $file_driver= $this->_objectManager->get('\Magento\Framework\Filesystem\Driver\File');

        $model = $this->_objectManager->create('Magebees\Layerednavigation\Model\Attributeoption');
        $option_model=$this->attributeoptionFactory->create();
        $opt_collection = $option_model->getCollection()
                            ->addFieldToFilter('store_id', $storeId)
                            ->addFieldToFilter('option_id', $optionId);
        $filedata=$this->getRequest()->getFiles()->toArray();
        
        if ($data) {
            if (isset($filedata['option_image']['name']) && $filedata['option_image']['name'] != '') {
                try {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' =>'option_image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('layerOption/images'));
                    unset($result['tmp_name']);
                    unset($result['path']);
                    $data['option_image'] = $result['file'];
                } catch (\Exception $e) {
                    $data['option_image'] = $filedata['option_image']['name'];
                }
            }
            
            if (isset($filedata['option_hover_image']['name']) && $filedata['option_hover_image']['name'] != '') {
                try {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' =>'option_hover_image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('layerOptionHover/images'));
                    unset($result['tmp_name']);
                    unset($result['path']);
                    $data['option_hover_image'] = $result['file'];
                } catch (\Exception $e) {
                    $data['option_hover_image'] = $filedata['option_hover_image']['name'];
                }
            }
            $attributes = $this->attrconfig->getEntityType('catalog_product')->getAttributeCollection()->addSetInfo()->addFieldToFilter('main_table.attribute_id', $attr_id);
            foreach ($attributes as $attr) {
                $options=$attr->setStoreId($storeId)->getSource()->getAllOptions(false);
            }
            
            foreach ($options as $opt) {
                if ($opt['value']==$optionId) {
                    $option_label=$opt['label'];
                }
            }
            if (empty($opt_collection->getData())) {
           
            $url_alias=$this->urlAliasAfterReplaceChar($data['url_alias']); 
            //$url_alias=$data['url_alias']; 
				$main_url_alias=trim(strtolower($data['url_alias']));
                $option_data = [
                'attribute_id' =>$attr_id,
                'option_id'=>$optionId,
                'option_label'=>$option_label ,
                'meta_title'=>trim($data['meta_title']),
                'meta_desc'=>trim($data['meta_desc']),
                'meta_keyword'=>trim($data['meta_keyword']),
                'main_url_alias'=>$main_url_alias,
                'url_alias'=>$url_alias,
                'store_id'=>$storeId
                ];
                if (isset($data['option_image'])) {
                    $option_data['option_image']=$data['option_image'];
                }
                if (isset($data['option_hover_image'])) {
                    $option_data['option_hover_image']=$data['option_hover_image'];
                }
                $option_model->setData($option_data)->save();
            } else {
                $editdata=$opt_collection->getData();
                $id=$editdata[0]['id'];
				 $url_alias=$this->urlAliasAfterReplaceChar($data['url_alias']);
				$main_url_alias=trim(strtolower($data['url_alias']));
                $resources = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection=$resources->getConnection();
                $updateData = [
                'meta_title'=>trim($data['meta_title']),
                'meta_desc'=>trim($data['meta_desc']),
                'meta_keyword'=>trim($data['meta_keyword']),
				'main_url_alias'=>$main_url_alias,
                'url_alias'=>$url_alias,
                'store_id'=>$storeId
                ];
                if (isset($data['option_image_delete'])) {
                    $trim_img=$editdata[0]['option_image'];
                    $val=$mediaDirectory->getAbsolutePath('layerOption/images');
                    $del_img=$val.$trim_img;
                    $file_driver->deleteFile($del_img);
                    $updateData['option_image']="";
                }
                if (isset($data['option_image'])) {
                    $updateData['option_image']=$data['option_image'];
                }
                if (isset($data['option_hover_image_delete'])) {
                    $trim_hover_img=$editdata[0]['option_hover_image'];
                    $val=$mediaDirectory->getAbsolutePath('layerOptionHover/images');
                    $del_hover_img=$val.$trim_hover_img;
                    $file_driver->deleteFile($del_hover_img);
                    $updateData['option_hover_image']="";
                }
                if (isset($data['option_hover_image'])) {
                    $updateData['option_hover_image']=$data['option_hover_image'];
                }
                $whereCondition = ['id=?'=>$id];
                $table=$resources->getTableName('magebees_layernav_attribute_option');
                $connection->update($table, $updateData, $whereCondition);
            }
            try {
                $this->messageManager->addSuccess(__('The Record has been saved.'));
                $this->cache_type->invalidate('magebees_layerednavigation');
                $this->_redirect('layerednavigation/manage/edit', [ 'id' => $attr_id]);
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Record.'));
            }
          
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
	public function getReplaceSpecialChar()
	{
		
		 $seoConfig = $this->_scopeConfig->getValue('layerednavigation/seo_setting', ScopeInterface::SCOPE_STORE);	
		
		if($seoConfig['replace_char']==0)
		{
			$replaceChar='-';
		}
		else
		{
			$replaceChar='_';
		}
		return $replaceChar;
	}
	public function IsReplaceAllSpecialChar()
	{
		$seoConfig = $this->_scopeConfig->getValue('layerednavigation/seo_setting', ScopeInterface::SCOPE_STORE);
		return $seoConfig['replace_char_all'];
		
	}
	public function urlAliasAfterReplaceChar($option_label)
	{
		
		$replaceChar=$this->getReplaceSpecialChar();		
		$isReplaceAllChar=$this->IsReplaceAllSpecialChar();
		
		if($isReplaceAllChar==0)
		{  
			
		//$label_alias = preg_replace('/[^a-zA-Z0-9]/s',$replaceChar, $option_label);
			$label_alias = preg_replace('/[^\w\s]+/u',$replaceChar,$option_label);
			//print_r($label_alias);die;
		$label_alias =preg_replace('/_+/',$replaceChar, $label_alias);
		$option_label=$label_alias;
		}
		$label_alias = str_replace(' ',$replaceChar, $option_label);
		$label_alias = preg_replace('/'.$replaceChar.'+/',$replaceChar, $label_alias);
		//$label_alias = str_replace('/',$replaceChar, $label_alias);
		$label_alias=strtolower($label_alias);
		return $label_alias;
	}
}
