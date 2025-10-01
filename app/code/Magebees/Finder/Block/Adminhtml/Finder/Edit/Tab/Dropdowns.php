<?php
namespace Magebees\Finder\Block\Adminhtml\Finder\Edit\Tab;

class Dropdowns extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_dropdowns;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magebees\Finder\Model\Dropdowns $dropdowns,
        array $data = []
    ) {
        $this->_dropdowns = $dropdowns;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('finder_data');
        $form = $this->_formFactory->create();
        if ($model->getId()) {
            $collection_of_dropdowns = $this->_dropdowns->getCollection();
            $collection_of_dropdowns->addFieldToFilter('finder_id', $model->getId());
            $check_records = count($collection_of_dropdowns->load()->getItems());
            if ($collection_of_dropdowns->count()) {
                foreach ($collection_of_dropdowns as $dropdowns) {
                    $dropdown_data[] = ['dropdown_id'=> $dropdowns->getId(),'name'=> $dropdowns->getName(),'sort'=> $dropdowns->getSort(),'range'=> $dropdowns->getRange()];
                }
            }
        }
	
        if ($model->getNumberOfDropdowns()) {
            $c = 0;
            for ($i=1; $i<=$model->getNumberOfDropdowns(); $i++) {
                $fieldset = $form->addFieldset('finder_form'.$i, ['legend' => __('Dropdown - '.$i)]);
                if (isset($dropdown_data)) {
                    $fieldset->addField(
                        'name'.$i,
                        'text',
                        [
                            'name' => 'name[]',
                            'label' => __('Name'),
                            'title' => __('Name'),
                            'value' => $dropdown_data[$c]['name'],
                            'required' => true,
                        ]
                    );
                    
                    $fieldset->addField(
                        'sort'.$i,
                        'select',
                        [
                            'label'     => __('Sort'),
                            'name'      => 'sort[]',
                            'values'    => [
                              [
                                  'value'     => 'asc',
                                  'label'     => __('Ascending'),
                              ],
                              [
                                  'value'     => 'desc',
                                  'label'     => __('Descending'),
                              ],
                            ],
                            'value' => $dropdown_data[$c]['sort']
                        ]
                    );
					
					        
                    $fieldset->addField(
                        'dropdown_id'.$i,
                        'hidden',
                        [
                            'name'      => 'ids[]',
                            'value' => $dropdown_data[$c]['dropdown_id']
                        ]
                    );
                } else {
                    $fieldset->addField(
                        'name'.$i,
                        'text',
                        [
                            'label'     => __('Name'),
                            'class'     => 'required-entry',
                            'required'  => true,
                            'name'      => 'name[]',
                            'required' => true,
                        ]
                    );
                    
                    $fieldset->addField(
                        'sort'.$i,
                        'select',
                        [
                            'label'     => __('Sort'),
                            'name'      => 'sort[]',
                            'values'    => [
                                [
                                  'value'     => 'asc',
                                  'label'     => __('Ascending'),
                                ],
                                [
                                  'value'     => 'desc',
                                  'label'     => __('Descending'),
                                ],
                            ],
                            'value' => ''
                        ]
                    );
					
					$fieldset->addField(
                        'dropdown_id'.$i,
                        'hidden',
                        [
                            'name'      => 'ids[]',
                            'value' => ''
                        ]
                    );
                }
                $c++;
            }
        }
        //$model_data = $model->getData();
        //$form->setValues($model_data);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
