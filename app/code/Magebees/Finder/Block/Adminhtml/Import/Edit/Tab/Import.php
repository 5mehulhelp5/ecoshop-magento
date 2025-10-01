<?php
namespace Magebees\Finder\Block\Adminhtml\Import\Edit\Tab;

class Import extends \Magento\Backend\Block\Widget\Form\Generic
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
		
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
		
    }
    
 	public function getImportedCSVFiles(){
        $files = array();
		$files[0] = "Please Select";
		$reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
		$path = $reader->getAbsolutePath("import/ymm");
		$count = 0;
		if(is_dir($path)){
			$dir = dir($path);
			while (false !== ($entry = $dir->read())) {
				if($entry != '.' && $entry != '..'){
					$file_parts = pathinfo($entry);
					if(isset($file_parts['extension']) && $file_parts['extension'] == 'csv'){
					//if($file_parts['extension'] == 'csv'){
						
						$files[] =  $file_parts['basename'];
					}
				}
				
			}
			
			
			sort($files);
			$dir->close();
			
		}
		return $files;
    }
	
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('fid');
        //$model = $this->_coreRegistry->registry('finder_data');
        $form = $this->_formFactory->create();
        
		$msg_fieldset = $form->addFieldset('finder_form_msg', ['legend'=>__('**Important Notes.')]);
             
        $msg_fieldset->addField(
            'note',
            'label',
            [
                'label'     => 'Note : ',
                'after_element_html' => '<h4 style="color:#df280a">If you select yes in "Delete Existing Data" dropdown then your all existing data are deleted. So please select yes if you want to delete all records... </h4>',
            ]
        );
		
        $fieldset = $form->addFieldset('import_form', ['legend'=>__('Import')]);
		//print_R($this->getImportedCSVFiles());
        $fieldset->addField(
            'existing_data',
            'select',
            [
              'label'     => __('Do You Want To Delete Existing Data?'),
              'name'      => 'existing_data',
              'values'    => [
                        [
                            'value'     => 0,
                            'label'     => __('No'),
                        ],

                        [
                            'value'     => 1,
                            'label'     => __('Yes'),
                        ],
                    ],
            ]
        );
		
		$fieldset->addField(
            'range_enable',
            'select',
            [
              'label'     => __('Do You Want To Import with Range?'),
              'name'      => 'range_enable',
              'values'    => [
                        [
                            'value'     => 0,
                            'label'     => __('No'),
                        ],

                        [
                            'value'     => 1,
                            'label'     => __('Yes'),
                        ],
                    ],
            ]
        );
                        
        
        $fieldset->addField(
            'import_file',
            'select',
            [
              'label'     => __('Choose file to import'),
              'name'      => 'import_file',
              'values'    => $this->getImportedCSVFiles(),
            ]
        );

        $fieldset->addField(
            'finder_id',
            'hidden',
            [
                'name'      => 'finder_id',
                'value'     => $id
            ]
        );
		/* Code Start For Set Custom Div In the form*/
    
        $fieldset->addType(
            'import_content',
            '\Magebees\Finder\Block\Adminhtml\Import\Edit\Renderer\ImportContent'
        );
        $fieldset->addField(
            'import',
            'import_content',
            [
                'name'  => 'import_content',
                'label' => __(''),
                'title' => __(''),

            ]
        );
        
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
