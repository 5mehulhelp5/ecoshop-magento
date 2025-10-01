<?php
namespace Magebees\Blog\Block\Adminhtml\Post\Edit;
class Tabs extends \Magento\Backend\Block\Widget\Tabs {
    protected function _construct() {
        parent::_construct();
        $this->setId('Post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Blog Post'));
    }
    protected function _beforeToHtml() {
        $this->addTab('form_section', ['label' => __('General'), 'title' => __('General'), 'content' => $this->getLayout()->createBlock('Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Form')->toHtml(), ]);
     
		$this->addTab('category_section',[
			'label' => __('Post Category'),
			'title' => __('Post Category'),
			'url' => $this->getUrl('blog/post/categorytab', ['_current' => true]),
			'class' => 'ajax'			
			]
		);
		$this->addTab('tag_section',[
			'label' => __('Post Tag'),
			'title' => __('Post Tag'),
			'url' => $this->getUrl('blog/post/tagtab', ['_current' => true]),
			'class' => 'ajax'			
			]
		);
		$this->addTab('product_section',[
			'label' => __('Related Products'),
			'title' => __('Related Products'),
			'url' => $this->getUrl('blog/post/producttab', ['_current' => true]),
			'class' => 'ajax'			
			]
		);
		$this->addTab('post_section',[
			'label' => __('Related Post'),
			'title' => __('Related Post'),
			'url' => $this->getUrl('blog/post/posttab', ['_current' => true]),
			'class' => 'ajax'			
			]
		);
		
		
		return parent::_beforeToHtml();
    }
}

