<?php
namespace Magebees\Finder\Block\Adminhtml\Product\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'tab/products.phtml';
    protected $_finderFactory;
    protected $_dropdownsFactory;
    protected $_mapvalueFactory;
    protected $_ymmvalueFactory;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magebees\Finder\Model\FinderFactory $finderFactory,
        \Magebees\Finder\Model\DropdownsFactory $dropdownsFactory,
        \Magebees\Finder\Model\MapvalueFactory $mapvalueFactory,
        \Magebees\Finder\Model\YmmvalueFactory $ymmvalueFactory,
        array $data = []
    ) {
        $this->_finderFactory = $finderFactory;
        $this->_dropdownsFactory = $dropdownsFactory;
        $this->_mapvalueFactory = $mapvalueFactory;
        $this->_ymmvalueFactory = $ymmvalueFactory;
        
        parent::__construct($context, $registry, $formFactory, $data);
    }
     
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        //$model = $this->_coreRegistry->registry('finder_value');
        $model = $this->_coreRegistry->registry('finder_data');
        $ymm_value_id =  $model->getYmmValueId();
        $map_value_id = $this->getRequest()->getParam('id');
        
        $fieldset = $form->addFieldset('product_fieldset', ['legend' => __('Product Information')]);
        
        $formdata = [];
            
        $fieldset->addField(
            'sku',
            'text',
            [
            'label'     => __('Sku'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'sku',
            ]
        );
        
        //for new form
        $finder_id = $this->getRequest()->getParam('fid');
        if ($finder_id) {
            $formdata['finder_id'] = $finder_id;
            $finder = $this->_finderFactory->create()->load($finder_id);
            foreach ($finder->getDropdowns($finder_id) as $dropdown) {
                $value  = 'value_'.$dropdown->getId();
                $label = 'label_'.$dropdown->getId();
                $fieldset->addField(
                    $value,
                    'text',
                    [
                    'label'    => __($dropdown->getName()),
                    'name'     => $label,
                    'class'     => 'required-entry',
                    'required'  => true,
                    ]
                );
            }
        }
        
        //for edit form
        //$mapmodel = $this->_mapvalueFactory->create()->load($map_value_id);
        if ($map_value_id) {
            $formdata['sku'] = $model->getSku();
        }
        
        while ($ymm_value_id) {
            $value  =   'value_' . $ymm_value_id;
            $label =   'label_'.$ymm_value_id;
             
            $ymmvalueModel = $this->_ymmvalueFactory->create()->load($ymm_value_id);
            $ymm_value_id = $ymmvalueModel->getParentId();
            $dropdownId = $ymmvalueModel->getDropdownId();
            $dropdown =  $this->_dropdownsFactory->create()->load($dropdownId);
            $formdata['finder_id'] = $dropdown->getFinderId();
            $dropdownName = $dropdown->getName();
            $formdata[$value] = $ymmvalueModel->getValue();
            
            $fieldset->addField(
                $value,
                'text',
                [
                'label'    => __($dropdownName),
                'name'     => $label,
                'class'     => 'required-entry',
                'required'  => true,
                ]
            );
        }
        
        
        /*$fieldset->addField('finder_id', 'hidden',
		[
			'name' => 'finder_id',
			'value'      => $formdata['finder_id'],
		]);*/
        
        $form->setValues($formdata);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Magebees\Finder\Block\Adminhtml\Product\Edit\Tab\Products\Grid',
                'products.grid'
            )
        );
        parent::_prepareLayout();
        return $this;
    }
    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
