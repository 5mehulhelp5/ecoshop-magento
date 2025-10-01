<?php
namespace Magebees\Blog\Block\Adminhtml\Comment\Edit;
class Form extends \Magento\Backend\Block\Widget\Form\Generic {
    protected function _prepareForm() {
        //Preparing the form here.
        $model = $this->_coreRegistry->registry('comment');
        $isElementDisabled = false;
        $data = array();
		if ($this->getRequest()->getParam('comment_id')) {
            $comment_id = $this->getRequest()->getParam('comment_id');
            $saveUrl = $this->getUrl("blog/comment/save", array('comment_id' => $comment_id));
        } else if ($this->getRequest()->getParam('parent_comment_id')) {
            $parent_comment_id = $this->getRequest()->getParam('parent_comment_id');
            $saveUrl = $this->getUrl("blog/comment/save", array('parent_comment_id' => $parent_comment_id));
			$data['status'] = 1;
			
        }else{
            $saveUrl = $this->getData('action');
        }
        $form = $this->_formFactory->create(['data' => ['id' => 'edit_form', 'enctype' => 'multipart/form-data', 'action' => $saveUrl, 'method' => 'post']]);
        $form->setHtmlIdPrefix('form_');
        $status = array('0' => 'Pending', '1' => 'Approved', '-1' => 'Not Approved');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Comment'), 'class' => 'fieldset-wide']);
        
		if ($model->getCommentId()) {
            $current_id = $model->getCommentId();
            $fieldset->addField('comment_id', 'hidden', ['name' => 'comment_id']);
            $data = $model->getData();
        }
		
        $fieldset->addType('commentinfo', '\Magebees\Blog\Block\Adminhtml\Comment\Edit\Renderer\Overview');
        $fieldset->addField('custom div', 'commentinfo', ['name' => 'customdiv', 'label' => __('Comment Information'), 'title' => __('Comment Information'), ]);
	
        $fieldset->addField('status', 'select', ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'), 'required' => true, 'values' => $status]);
        $fieldset->addField('text', 'textarea', ['name' => 'text', 'label' => __('Comment'), 'title' => __('Comment'), 'required' => true,'rows'=>20,'columns'=>100]);
        $fieldset->addField('creation_time','date',['name' => 'creation_time','label' => __('Creation Date'),'required' => true,'title' => __('Creation Date'),'date_format' => 'yyyy-MM-dd ','time_format' => 'HH:mm:ss']);
		$form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

