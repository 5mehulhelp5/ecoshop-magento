<?php

namespace Magebees\PocoThemes\Model\Import;

use Magento\Framework\DataObject;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as BlockCollectionFactory;
use Magento\Cms\Model\BlockFactory as BlockFactory;
use Magento\Cms\Model\ResourceModel\Block as BlockResourceBlock;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Cms\Model\PageFactory as PageFactory;
use Magento\Cms\Model\ResourceModel\Page as PageResourceBlock;

class Cms
{
    protected $_importPath; 
    
    protected $_parser;
    
    protected $_blockCollectionFactory;

    protected $_blockRepository;
    
    protected $_blockFactory;
    
    protected $_pageCollectionFactory;

    protected $_pageRepository;
    
    protected $_pageFactory;
	protected $pocoHelper;
	protected $moduleReader;
    
    public function __construct(
        \Magebees\PocoBase\Helper\Data $pocoHelper,
        BlockCollectionFactory $blockCollectionFactory,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        BlockFactory $blockFactory,
        PageCollectionFactory $pageCollectionFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        PageFactory $pageFactory,
		\Magento\Framework\Module\Dir\Reader $moduleReader
		
    ) {
        $this->pocoHelper = $pocoHelper;
		$this->_blockCollectionFactory = $blockCollectionFactory;
        $this->_blockFactory = $blockFactory;
        $this->_blockRepository = $blockRepository;
        $this->_pageCollectionFactory = $pageCollectionFactory;
        $this->_pageFactory = $pageFactory;
        $this->_pageRepository = $pageRepository;
		$this->moduleReader = $moduleReader;
        $this->_parser = new \Magento\Framework\Xml\Parser();
	}

     public function getDirectory()
    {
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Magebees_PocoThemes'
        );
        return $viewDir;
    }

    public function importCms($type,$overwrite)   {
		// Default response
        $gatewayResponse = new DataObject([
            'is_valid' => false,
            'import_path' => '',
            'request_success' => false,
            'request_message' => __('Error during Import CMS Sample Datas.'),
        ]);

        try {
            
            $this->_importPath = $this->getDirectory();
            $xmlPath = $this->_importPath .'/importdata/cms/'. $type . '.xml';
            
            if (!is_readable($xmlPath)) {
                throw new \Exception(
                    __("Can't get the data file for import cms blocks/pages: ".$xmlPath)
                );
            } 
            $data = $this->_parser->load($xmlPath)->xmlToArray();
			
			
			$cms_collection = null;
            $conflictingOldItems = array();
            
            $i = 0;
            foreach($data['root'][$type]['cms_item'] as $_item) {
				$exist = false;
				
					if($type == "blocks") {
						$cms_collection = $this->_blockCollectionFactory->create()->addFieldToFilter('identifier', $_item['identifier']);
						if(count($cms_collection) > 0)
							$exist = true;

					}else {
						$cms_collection = $this->_pageCollectionFactory->create()->addFieldToFilter('identifier', $_item['identifier']);
						if(count($cms_collection) > 0)
							$exist = true;

					}
					
					if($overwrite) {
						if($exist) {
							$conflictingOldItems[] = $_item['identifier'];
							
					
							if($type == "blocks"){
								//$this->_blockRepository->deleteById($_item['identifier']);
								$blockmodel = $this->_blockFactory->create()->load($_item['identifier']);	
								$cmsblockid = $blockmodel->getId();
                                if ($cmsblockid) {
                                    $blockmodel->load($cmsblockid);
									$blockmodel->addData($_item);
									$blockmodel->save();
                                }
							}else{
								//$this->_pageRepository->deleteById($_item['identifier']);
								$cmsmodel = $this->_pageFactory->create()->load($_item['identifier']);
								$cmspageid = $cmsmodel->getId();
                                if ($cmspageid) {
                                    $cmsmodel->load($cmspageid);
									$cmsmodel->addData($_item);
									$cmsmodel->save();
                                }
							}
							
							
							
					
						}
					} else {
						if($exist) {
							$conflictingOldItems[] = $_item['identifier'];
							continue;
						}
					}
					
					if (!in_array($_item['identifier'], $conflictingOldItems)) {
					$_item['stores'] = [0];
					if($type == "blocks") {
						$this->_blockFactory->create()->setData($_item)->save();
					} else {
						$this->_pageFactory->create()->setData($_item)->save();
					}	
					}
					
					$i++;
				
            }
            $message = "";
            if ($i)
                $message = $i." item(s) was(were) imported.";
            else
                $message = "No items were imported.";
            
            $gatewayResponse->setIsValid(true);
            $gatewayResponse->setRequestSuccess(true);

            if ($gatewayResponse->getIsValid()) {
                if ($overwrite){
                    if($conflictingOldItems){
                        $message .= "Items (".count($conflictingOldItems).") with the following identifiers were overwritten:<br/>".implode(', ', $conflictingOldItems);
                    }
                } else {
                    if($conflictingOldItems){
                        $message .= "<br/>Unable to import items (".count($conflictingOldItems).") with the following identifiers (they already exist in the database):<br/>".implode(', ', $conflictingOldItems);
                    }
                }
				$this->pocoHelper->cachePrograme();
            }
            $gatewayResponse->setRequestMessage(__($message));
        } catch (\Exception $exception) {
            $gatewayResponse->setIsValid(false);
            $gatewayResponse->setRequestMessage($exception->getMessage());
        }
        return $gatewayResponse;
    }
}