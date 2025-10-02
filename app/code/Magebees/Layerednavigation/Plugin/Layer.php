<?php

namespace Magebees\Layerednavigation\plugin;

use Magento\Store\Model\ScopeInterface;

class Layer
{
    public function __construct(
        private \Magento\Catalog\Helper\Data $catalogHelper,
        private \Magento\Framework\App\Config\ScopeConfigInterface $storeManager,
        private \Magento\Framework\App\Request\Http $request,
        private \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
    }

    public function afterGetProductCollection(
        \Magento\Catalog\Model\Layer $subject,
        $result
    ) {
        $param = $this->request->getParams();
        if (array_key_exists("product_list_limit", $param)) {

        } else {
            $size = $this->storeManager->getValue(
                'catalog/frontend/grid_per_page',
                ScopeInterface::SCOPE_STORE
            );
            if ($size) {
                $result->setPageSize($size);
            }
        }

        if (array_key_exists("product_list_order", $param)) {
            $currentOrder = $param['product_list_order'];
        } else {

            if (array_key_exists("id", $param)) {
                $category = $this->catalogHelper->getCategory();
                if ($category === null && !empty($param['id'])) {
                    $category = $this->getCategory((int)$param['id']);
                }
                $category_data = $category->getData();
                if (array_key_exists("default_sort_by", $category_data)) {
                    $currentOrder = $category->getData('default_sort_by');
                } else {
                    $currentOrder = 'position';
                }
            } else {
                $currentOrder = 'position';
            }
        }

        if (array_key_exists("product_list_dir", $param)) {
            $product_list_dir = $param['product_list_dir'];
        } else {
            $product_list_dir = 'asc';
        }

        if (!$currentOrder) {
            $currentOrder = 'position';
        }
        $result->setOrder($currentOrder, $product_list_dir);

        if (array_key_exists("p", $param)) {
            $result->setCurPage($param['p']);
        }

        return $result;
    }

    private function getCategory(int $categoryId)
    {
        $category = $this->categoryFactory->create()->load($categoryId);
    }
}
