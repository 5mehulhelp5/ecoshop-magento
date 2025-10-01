<?php
namespace Magebees\Blog\Block\Adminhtml\Urlrewrite\Edit;
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('Url_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Blog Url Rewrite'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', [
        'label'     => __('General'),
        'title'     => __('General'),
        'content'   => $this->getLayout()->createBlock('Magebees\Blog\Block\Adminhtml\Urlrewrite\Edit\Tab\Form')->toHtml(),
        ]);
		
        return parent::_beforeToHtml();
    }
}
