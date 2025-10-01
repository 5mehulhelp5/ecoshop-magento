<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\System\Config\TrustSeals;
use Magebees\Onepagecheckout\Block\Adminhtml\System\Config\TrustSeals\Renderer\Badge;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class Badges extends AbstractFieldArray
{
    private $badgeRenderer;
    protected $_template = 'system/config/trust_seals/field_array.phtml';
    protected function _prepareToRender()
    {
        $this->addColumn(
            'script',
            [
                'label' => __('Trust Seal'),
                'renderer' => $this->getBadgeRenderer()
            ]
        );
        $this->_addAfter = false;
    }
    private function getBadgeRenderer()
    {
        if (!$this->badgeRenderer) {
            $this->badgeRenderer = $this->getLayout()->createBlock(
                Badge::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->badgeRenderer;
    }
    public function getDefaultRowData()
    {
        $result = [];
        $columns = $this->getColumns();
        foreach (array_keys($columns) as $columnName) {
            $result[$columnName] = '';
        }
        return $result;
    }
    public function getRows()
    {
        $rows = [];
        foreach ($this->getArrayRows() as $row) {
            $rows[] = $row->getData();
        }
        return $rows;
    }
}