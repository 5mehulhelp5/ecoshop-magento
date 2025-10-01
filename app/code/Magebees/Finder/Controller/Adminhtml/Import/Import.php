<?php
namespace Magebees\Finder\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Import extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $resource;
    protected $connection;
	protected $timezoneInterface;
 
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resource,TimezoneInterface $timezoneInterface
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
		$this->timezoneInterface = $timezoneInterface;
        parent::__construct($context);
    }



 
    public function execute()
    {
		$result = array();
        $post = $this->getRequest()->getPost()->toArray();
		$range_enable = $post['range_enable']; //need to get from request
		$dropdownsCol = $this->_objectManager->create('Magebees\Finder\Model\Dropdowns')->getCollection();
        $dropdownsCol->addFieldToFilter('finder_id', $post['finder_id']);
        $dropdown_ids = $dropdownsCol->load()->getColumnValues('dropdown_id');
		
		if(isset($post['import_file'])){
			$processStartTime = $this->getCurrentTime();
			$filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
			$reader = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
			$file = $reader->getAbsolutePath("import/ymm/".urldecode($post['import_file']));
			$handle = fopen($file,'r');
			
			//set the pointer for read row after save 500 data in new ajax request
			if(isset($post['pointer_next']) && $post['pointer_next']!=1){
				$flag = false;
				fseek($handle,$post['pointer_next']);
			}else{
				$flag = true;
			}
			
			$insertData = array();
		
			if ($flag) { //skip first row of headers
				$checkcsv = fgetcsv($handle);
				$dataincsv = count($checkcsv) - 1;
				if ($dataincsv != count($dropdown_ids)) {
					$this->messageManager->addError(__('CSV columns mismatch. Please check CSV file and upload again.'));
					$result['fail'] = 'CSV columns mismatch. Please check CSV file and import again.';
					$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
					return;
				}
				//$flag = false;
			}
				
			$this->processCSV($handle, $range_enable,$dropdown_ids);
            $processEndTime = $this->getCurrentTime();
			$historyModel = $this->_objectManager->create('Magebees\Finder\Model\History');
			$history_data['filename'] = $post['import_file'];
			$history_data['finder_id'] = $post['finder_id'];
			$history_data['started_time'] = $processStartTime;
			$history_data['finished_time'] = $processEndTime;
			$historyModel->setData($history_data)->save();
		}
	}
	
	public function processCSV($handle, $range_enable, $dropdown_ids){
		
		$count = 1;
		$range_arr_cnt = 0;
		$rangeToArray = [];
		$combinations = [];
		//while loop start for get CSV data
		while (($data = fgetcsv($handle,0, ",")) !== false) {
			if($count > 500){
				break;
			}
			if($range_enable){
				//identify range data and conver to array
				$fid= 1;//column number in CSV
				foreach ($dropdown_ids as $dropdown_id) {
					$rangeToArray[$range_arr_cnt]['SKU'] = trim($data[0]);
					$drop_val = trim($data[$fid]);
					if(strpos($drop_val, '-')!== false) {
						$minMax = explode('-',$drop_val);
						
						// Ensure that both min and max are numeric before proceeding
						if (is_numeric($minMax[0]) && is_numeric($minMax[1]) && $minMax[0] <= $minMax[1]) {
							$rangeToArray[$range_arr_cnt][$dropdown_id] = range((float) $minMax[0], (float) $minMax[1]);
						}
						
					}else{
						$rangeToArray[$range_arr_cnt][$dropdown_id] = array($drop_val);
					}
					$fid++;
				}
				$range_arr_cnt++;
			}else{
				$combinations[] =  $data;
			}
			$count++;
		}
		
		
		//associte rane array with SKU and other ymm elements, create combinations
		if($range_enable){
			$inputArray = $rangeToArray;
			$desiredIndices = $dropdown_ids;
			foreach ($inputArray as $subArray) {
				$arrays = array();
				foreach ($desiredIndices as $index) {
					$arrays[] = $subArray[$index];
				}

				$temp = array();
				$result = array();
				foreach ($arrays as $array) {
					if (!is_array($array)) {
						$array = array($array);
					}
					if (empty($temp)) {
						foreach ($array as $val) {
							$temp[] = array($val);
						}
					} else {
						foreach ($temp as $item) {
							foreach ($array as $val) {
								$result[] = array_merge($item, array($val));
							}
						}
						$temp = $result;
						$result = array();
					}
				}
				
				foreach ($temp as $combination) {
					array_unshift($combination, $subArray['SKU']); // Add SKU value
					$combinations[] = $combination;
				}
			}
		}
		
		$this->saveCombinations($combinations, $dropdown_ids, $handle);
	}
	
	public function saveCombinations($combinations, $dropdown_ids, $handle){
		$insertData = [];
		foreach($combinations as $data){
			$fid= 1;
			$parent_id = 0;
			$value_data = [];
			try {
				foreach ($dropdown_ids as $dropdown_id) {
					$valueModel = $this->_objectManager->create('Magebees\Finder\Model\Ymmvalue');
					$exits = $valueModel->getCollection()
						->addFieldToFilter('value', trim($data[$fid]))
						->addFieldToFilter('parent_id', $parent_id)
						->addFieldToFilter('dropdown_id', $dropdown_id);
					$isExists = $exits->getData();
					if (empty($isExists)) {
						$value_data['dropdown_id'] = $dropdown_id;
						$value_data['parent_id'] = $parent_id;
						$value_data['value'] = trim($data[$fid]);
						$valueModel->setData($value_data)->save();
						$parent_id = $valueModel->getId();
					} else {
						$parent_id = $exits->getFirstItem()->getYmmValueId();
					}
					$fid++;
				}
			} catch (\Exception $e) {
				//$this->messageManager->addException($e, __('Something went wrong while saving the product.'));
				$this->messageManager->addError($e->getMessage());
				$result['fail'] = $e->getMessage();
				$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
				return;
			}
			
			//prepare data to map table
			//Updated for solve import issue from v1.5.1
			$ymm_value_id = $parent_id;
			$mapModel = $this->_objectManager->create('Magebees\Finder\Model\Mapvalue')->load($ymm_value_id, 'ymm_value_id');
			$map_value = array();
			$map_value['ymm_value_id'] = $ymm_value_id;
			$map_value['sku'] = $data[0];
			$post_value = explode("|",$data[0]);
			$product_ids = array();
			$check = array_map(array($this,"getYmmValueIds"), $insertData);
			$flag_new = false;

			if(in_array($map_value['ymm_value_id'],$check)){
				$flag_new = true;
				$check_flip = array_flip($check);
				$new_key = $check_flip[$map_value['ymm_value_id']];
				$map_value['sku'] = $insertData[$new_key]['sku']."|".$map_value['sku'];
			}else{
				$map_value['sku'] = $mapModel->getSku()."|".$map_value['sku'];
				$map_value['sku']= implode("|", array_unique(explode("|", $map_value['sku'])));
			}
			$map_value['sku'] = array_unique(explode("|",$map_value['sku']));

			foreach($map_value['sku'] as $sku){
				$product_ids[] = $this->_objectManager->create('Magento\Catalog\Model\Product')->getIdBySku(trim($sku));
			}
			$map_value['sku'] = implode("|",$map_value['sku']);
			$map_value['sku'] = trim($map_value['sku'],"|");
			$map_value['product_id'] = implode("|",$product_ids);
			$map_value['product_id'] = trim($map_value['product_id'],"|");

			if($mapModel->getId()){
				if($flag_new){
					$insertData[$new_key] = array('map_value_id' => $mapModel->getId(),'ymm_value_id' => $map_value['ymm_value_id'], 'sku' => $map_value['sku'],'product_id' => $map_value['product_id']);
				}else{
					$insertData[] = array('map_value_id' => $mapModel->getId(),'ymm_value_id' => $map_value['ymm_value_id'], 'sku' => $map_value['sku'],'product_id' => $map_value['product_id']);
				}
			}else{
				if($flag_new){
					$insertData[$new_key] = array('map_value_id' => '','ymm_value_id' => $map_value['ymm_value_id'], 'sku' => $map_value['sku'],'product_id' => $map_value['product_id']);
				}else{
					$insertData[] = array('map_value_id' => '','ymm_value_id' => $map_value['ymm_value_id'], 'sku' => $map_value['sku'],'product_id' => $map_value['product_id']);
				}
			}
		}
		
		//save to map table
		if (!empty($insertData)) {
			try {
				$this->connection->beginTransaction();
				$this->connection->insertOnDuplicate($this->resource->getTableName('magebees_finder_map_value'), $insertData);
				//insertOnDuplicate => update if duplicate key found, also can use insertMultiple
				$this->connection->commit();
				$result['count'] = count($insertData);
				$result['pointer_last'] = ftell($handle);
				$next = fgets($handle);
				if($next){
					$result['no_more'] =false;
				}else{
					$this->messageManager->addSuccess(__('YMM product(s) has been imported.'));
					$result['no_more'] =true;
				}
				$insertData = array();

			} catch (\Exception $e) {
				$this->connection->rollBack();
				$result['fail'] = $e->getMessage();
				$this->messageManager->addError($e->getMessage());
			}
			$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
			return;
		}
	}
   
	public function getYmmValueIds($insertData) { return $insertData["ymm_value_id"]; }
	
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
	
	public function getCurrentTime(){
		$formatDate = $this->timezoneInterface->formatDate();
        // you can also get format wise date and time
        $dateTime = $this->timezoneInterface->date()->format('Y-m-d H:i:s');
        $date = $this->timezoneInterface->date()->format('Y-m-d');
        $time = $this->timezoneInterface->date()->format('H:i');
        return $dateTime;
	}
}
