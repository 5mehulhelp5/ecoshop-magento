<?php
namespace Magebees\Blog\Block\Adminhtml\Comment\Edit;
class Tabs extends \Magento\Backend\Block\Widget\Tabs {
    protected function _construct() {
        parent::_construct();
        $this->setId('Comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Blog Comment'));
    }
    protected function _beforeToHtml() {
        $this->addTab('form_section', ['label' => __('General'), 'title' => __('General'), 'content' => $this->getLayout()->createBlock('Magebees\Blog\Block\Adminhtml\Comment\Edit\Form')->toHtml(), ]);
        return parent::_beforeToHtml();
    }
}

