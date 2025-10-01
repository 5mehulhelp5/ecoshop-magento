<?php

namespace Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer; 

class Category extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var bool
     */
    protected $_skipAllStoresLabel = false;

    /**
     * @var bool
     */
    protected $_skipEmptyStoresLabel = false;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $category;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
	
	
	
	
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magebees\Blog\Model\Category $category,
        array $data = []
    ) {
        $this->category = $category;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve System Store model
     *
     * @return \Magento\Store\Model\System\Store
     */
    protected function _getStoreModel()
    {
        return $this->_systemStore;
    }

    /**
     * Retrieve 'show all stores label' flag
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function _getShowAllStoresLabelFlag()
    {
        return $this->getColumn()->getData(
            'skipAllStoresLabel'
        ) ? $this->getColumn()->getData(
            'skipAllStoresLabel'
        ) : $this->_skipAllStoresLabel;
    }

    /**
     * Retrieve 'show empty stores label' flag
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function _getShowEmptyStoresLabelFlag()
    {
        return $this->getColumn()->getData(
            'skipEmptyStoresLabel'
        ) ? $this->getColumn()->getData(
            'skipEmptyStoresLabel'
        ) : $this->_skipEmptyStoresLabel;
    }

    /**
     * Render row store views
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $out = '';

        // $origStores =$row->getData($this->getColumn()->getIndex());
        $categoriesIds = explode(',',(string)$row->getData($this->getColumn()->getIndex()));
		foreach($categoriesIds as $id):
				$category = $this->category->load($id);
				$out .= $category->getTitle() . '<br/>';
		endforeach;
		return $out;
	}

    /**
     * Render row store views for export
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function renderExport(\Magento\Framework\DataObject $row)
    {
        $out = '';
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        // $origStores = $row->getData($this->getColumn()->getIndex());
        $origStores = explode(',',(string)$row->getData($this->getColumn()->getIndex()));

        if ($origStores === null && $row->getStoreName()) {
            $scopes = [];
            foreach (explode("\n", (string)$row->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat(' ', $k * 3) . $label;
            }
            $out .= implode("\r\n", (array)$scopes) . __(' [deleted]');
            return $out;
        }

        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }

        if (in_array(0, $origStores) && !$skipAllStoresLabel) {
            return __('All Store Views');
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'] . "\r\n";
            foreach ($website['children'] as $group) {
                $out .= str_repeat(' ', 3) . $group['label'] . "\r\n";
                foreach ($group['children'] as $store) {
                    $out .= str_repeat(' ', 6) . $store['label'] . "\r\n";
                }
            }
        }

        return $out;
    }
}
