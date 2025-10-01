<?php

namespace Magebees\Layerednavigation\Plugin\CatalogSearch\Model\Search\FilterMapper;

use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Eav\Model\Config as EavConfig;


class CustomFilterExclusion
{
    
    private $fresourceConnection;
    private $storeManager;
    private $customerSession;
    private $fconditionManager;
    private $eavConfig;
    private $localeDate;
    private $validFields = ['rating_summary'];
    private $productIdLink;

  
    public function __construct(
        \Magento\Framework\App\ResourceConnection $fresourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        ConditionManager $fconditionManager,
        EavConfig $eavConfig
    ) {
        $this->fresourceConnection = $fresourceConnection;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->localeDate = $localeDate;
        $this->fconditionManager = $fconditionManager;
        $this->eavConfig = $eavConfig;
        $this->productIdLink = $productMetadata->getEdition() == 'Enterprise' ? 'row_id' : 'entity_id';
    }

    
    public function apply(
        \Magento\Framework\Search\Request\FilterInterface $filterObj,
        \Magento\Framework\DB\Select $select
    ) {
        if (!in_array($filterObj->getField(), $this->validFields, true)) {
            return false;
        }

        switch ($filterObj->getField()) {        
            
            case 'rating_summary':
                $isApplied = $this->applyRatingFilterLayer($filterObj, $select);
                break;
            default:
                $isApplied = false;
        }

        return $isApplied;
    }

   
    private function applyRatingFilterLayer(
        \Magento\Framework\Search\Request\FilterInterface $filterObj,
        \Magento\Framework\DB\Select $select
    ) {
        $alias = $filterObj->getField() . RequestGenerator::FILTER_SUFFIX;
        $storeId=$this->storeManager->getStore()->getId();
        $select->joinLeft(
            [$alias => $this->fresourceConnection->getTableName('review_entity_summary')],
            sprintf(
                '`rating_summary_filter`.`entity_pk_value`=`search_index`.entity_id
                AND `rating_summary_filter`.entity_type = 1
                AND `rating_summary_filter`.store_id  =  %d',
                $storeId
            ),
            []
        );

        return true;
    }

    private function getAttributeId($pattributeCode)
    {
        $attrData = $this->eavConfig->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $pattributeCode
        );

        return (int) $attrData->getId();
    }

  
    private function extractTableAliasFromSelect(\Magento\Framework\DB\Select $select)
    {
        $fromArray = array_filter(
            $select->getPart(\Magento\Framework\DB\Select::FROM),
            function ($fromPartData) {
                return $fromPartData['joinType'] === \Magento\Framework\DB\Select::FROM;
            }
        );

        return $fromArray ? array_keys($fromArray)[0] : null;
    }
}
