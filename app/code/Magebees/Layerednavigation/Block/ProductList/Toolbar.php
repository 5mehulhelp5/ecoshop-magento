<?php
namespace Magebees\Layerednavigation\Block\ProductList;
use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
   
    public function getWidgetOptionsJson(array $customOptions = [])
    {
		// overwrite for fix issue for encoded url in pagination url
        $defaultMode = $this->_productListHelper->getDefaultViewMode($this->getModes());
        $options = [
            'mode' => ToolbarModel::MODE_PARAM_NAME,
            'direction' => ToolbarModel::DIRECTION_PARAM_NAME,
            'order' => ToolbarModel::ORDER_PARAM_NAME,
            'limit' => ToolbarModel::LIMIT_PARAM_NAME,
            'modeDefault' => $defaultMode,
            'directionDefault' => $this->_direction ?: ProductList::DEFAULT_SORT_DIRECTION,
            'orderDefault' => $this->getOrderField(),
            'limitDefault' => $this->_productListHelper->getDefaultLimitPerPageValue($defaultMode),
            //'url' => $this->$this->getPagerUrl(),
            'url' => json_decode(html_entity_decode($this->getPagerUrl())),
        ];
        $options = array_replace_recursive($options, $customOptions);
        return json_encode(['productListToolbarForm' => $options]);
    }
}