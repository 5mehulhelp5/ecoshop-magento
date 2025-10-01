<?php
namespace Magebees\Advertisementblock\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	protected $_storeManager;
	protected $request;
	protected $_filesystem;
	protected $_imageFactory;
	protected $advertisementimagesFactory;
	protected $advertisementinfoFactory;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magebees\Advertisementblock\Model\AdvertisementimagesFactory $advertisementimagesFactory,
		\Magebees\Advertisementblock\Model\AdvertisementinfoFactory $advertisementinfoFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->request =$context->getRequest();
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
		$this->advertisementimagesFactory = $advertisementimagesFactory;
        $this->advertisementinfoFactory = $advertisementinfoFactory;
        parent::__construct($context);
    }
    public function getImageMediaDir()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path=$mediaDirectory.'magebees_advertisement';
        return $path;
    }
    public function getResizedImgDir()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path=$mediaDirectory.'magebees_advertisement/resized';
        return $path;
    }
    public function getEffectArr()
    {
        $effect_arr=[];
        $effect_arr['zoomin']='Zoom In';
        $effect_arr['zoomout']='Zoom Out';
        $effect_arr['slide']='Slide';
        $effect_arr['rotate']='Rotate';
        $effect_arr['opacity']='Opacity';
        $effect_arr['flashing']='Flashing';
        $effect_arr['shine']='Shine';
        $effect_arr['circle']='Circle';
        return $effect_arr;
    }
    public function getTextPositionArr()
    {
        $text_position_arr=[];
        $text_position_arr['0']='Top';
        $text_position_arr['1']='Middle';
        $text_position_arr['2']='Bottom';
        return $text_position_arr;
    }
    
  
    public function getNumberOfBlock()
    {
        $block_arr=[];
        $block_arr[1]=1;
        $block_arr[2]=2;
        $block_arr[3]=2;
        $block_arr[4]=2;
        $block_arr[5]=2;
        $block_arr[6]=3;
        $block_arr[7]=3;
        $block_arr[8]=3;
        $block_arr[9]=3;
        $block_arr[10]=3;
        $block_arr[11]=4;
        $block_arr[12]=4;
        $block_arr[13]=4;
        $block_arr[14]=4;
        $block_arr[15]=4;
        $block_arr[16]=4;
        $block_arr[17]=5;
        $block_arr[18]=5;
        $block_arr[19]=6;
        $block_arr[20]=6;
        $block_arr[21]=6;
        $block_arr[22]=8;
        $block_arr[23]=12;
        $block_arr[24]=9;
        $block_arr[25]=6;
        $block_arr[26]=5;
        $block_arr[27]=1;
        $block_arr[28]=5;
        $block_arr[29]=5;
        $block_arr[30]=3;	
		$block_arr[31]=2;
		$block_arr[32]=5;
		$block_arr[33]=3;
		$block_arr[34]=3;
		$block_arr[35]=2;
		$block_arr[36]=2;
		$block_arr[37]=3;
		return $block_arr;
    }
    public function getPatternImageSize()
    {
        $size_arr=[];
        $size_arr[1]=['1200*360'];
        $size_arr[2]=['590*300','590*300'];
        $size_arr[3]=['1200*200','1200*200'];
        $size_arr[4]=['590*580','590*580'];
        $size_arr[5]=['340*340','840*340'];
        $size_arr[6]=['390*421','810*421','390*421'];
        $size_arr[7]=['530*283','530*283','530*283'];
        $size_arr[8]=['270*500','620*500','270*500'];
        $size_arr[9]=['910*560','270*270','270*270'];
        $size_arr[10]=['590*300','590*300','1200*200'];
        $size_arr[11]=['285*500','285*500','285*500','285*500'];
        $size_arr[12]=['590*240','590*240','590*240','590*240'];
        $size_arr[13]=['600*250','600*250','280*520','280*520'];
        $size_arr[14]=['280*500','600*240','600*240','280*500'];
        $size_arr[15]=['280*400','600*400','280*400','1200*120'];
        $size_arr[16]=['590*560','590*270','295*270','295*270'];
        $size_arr[17]=['280*560','600*270','280*270','280*270','600*270'];
        $size_arr[18]=['285*250','285*250','285*250','285*250','1200*180'];
        $size_arr[19]=['600*270','280*270','280*270','280*270','280*270','600*270'];
        $size_arr[20]=['285*270','285*270','285*270','285*270','590*200','590*200'];
        $size_arr[21]=['285*280','285*280','285*280','285*280','285*580','285*580'];
        $size_arr[22]=['285*270','285*270','285*270','285*270','285*270','285*270','285*270','285*270'];
        $size_arr[23]=['285*270','285*270','285*270','285*270','285*270','285*270','285*270','285*270','285*270','285*270','285*270','285*270'];
        $size_arr[24]=['385*385','385*385','385*385','385*385','385*385','385*385','385*385','385*385','385*385'];
        $size_arr[25]=['590*300','590*300','285*270','285*270','285*270','285*270'];
        $size_arr[26]=['400*310','400*310','360*640','400*310','400*310'];
        $size_arr[27]=['270*400'];
        $size_arr[28]=['564*527','276*317','276*190','276*190','276*317'];
        $size_arr[29]=['660*441','660*441','430*450','430*450','430*450'];		
		$size_arr[30]=['463*290','463*290','463*290'];	
		$size_arr[31]=['710*218','710*218'];
		$size_arr[32]=['387*337','387*337','387*337','387*337','805*704'];
		$size_arr[33]=['530*415','530*415','530*415'];
		$size_arr[34]=['450*262','450*262','450*262'];
		$size_arr[35]=['710*338','710*338'];
		$size_arr[36]=['708*294','708*294'];
		$size_arr[37]=['463*433','463*433','463*433'];
		return $size_arr;
    }


    public function getPatternImgDiv()
    {
        $pattern_div_arr=[];
          $pattern_div_arr[13]=['mageb-sub-grid mageb-three-col-1-1','mageb-sub-grid mageb-three-col-1-2','mageb-grid mageb-three-col-2','mageb-grid mageb-three-col-3'];
          $pattern_div_arr[14]=['mageb-grid mageb-three-col-1','mageb-sub-grid mageb-three-col-2-1','mageb-sub-grid mageb-three-col-2-2','mageb-grid mageb-three-col-3'];
          $pattern_div_arr[16]=['mageb-grid mageb-two-col-1','mageb-sub-grid mageb-two-col-2-1','mageb-sub-grid mageb-two-col-2-2','mageb-sub-grid mageb-two-col-2-3'];
          $pattern_div_arr[17]=['mageb-grid mageb-two-col-1','mageb-sub-grid mageb-two-col-2-1','mageb-sub-grid mageb-two-col-2-2','mageb-sub-grid mageb-two-col-2-3','mageb-sub-grid mageb-two-col-2-4'];
          //$pattern_div_arr[19]=array('mageb-sub-grid mageb-two-col-1-1','mageb-sub-grid mageb-two-col-2-1','mageb-sub-grid mageb-two-col-2-2','mageb-sub-grid mageb-two-col-1-2','mageb-sub-grid mageb-two-col-1-3','mageb-sub-grid mageb-two-col-2-3');
          //$pattern_div_arr[20]=array('mageb-sub-grid mageb-two-col-1-1','mageb-sub-grid mageb-two-col-1-2','mageb-sub-grid mageb-two-col-2-1','mageb-sub-grid mageb-two-col-2-2','mageb-sub-grid mageb-two-col-1-3','mageb-sub-grid mageb-two-col-2-3');
          $pattern_div_arr[21]=['mageb-sub-grid mageb-three-col-1-1','mageb-sub-grid mageb-three-col-1-2','mageb-sub-grid mageb-three-col-1-3','mageb-sub-grid mageb-three-col-1-4','mageb-sub-grid mageb-three-col-2-1','mageb-sub-grid mageb-three-col-2-1'];
         $pattern_div_arr[26]=['mageb-sub-grid mageb-three-col-1-1','mageb-sub-grid mageb-three-col-1-2','mageb-sub-grid mageb-three-col-1-3','mageb-sub-grid mageb-three-col-1-4','mageb-sub-grid mageb-three-col-2-1'];
         $pattern_div_arr[28]=['mageb-sub-grid mageb-three-col-1-1','mageb-sub-grid mageb-three-col-1-2','mageb-sub-grid mageb-three-col-1-3','mageb-sub-grid mageb-three-col-1-4','mageb-sub-grid mageb-three-col-2-1'];
		 $pattern_div_arr[32]=['mageb-grid mageb-two-col-1','mageb-sub-grid mageb-two-col-1-1','mageb-sub-grid mageb-two-col-1-2','mageb-sub-grid mageb-two-col-1-3','mageb-sub-grid mageb-two-col-2-4'];
        return $pattern_div_arr;
    }
    public function getColumnParentDiv()
    {
        //$pattern_div_arr[13]=array( array("col-1","0,1"),array("col-2",""),array("col-3",""));
        $pattern_div_arr[13]=[['0,1','mageb-grid mageb-three-col-1']];
        $pattern_div_arr[14]=[['1,2','mageb-grid mageb-three-col-2']];
        $pattern_div_arr[16]=[['1,2,3','mageb-grid mageb-two-col-2']];
        $pattern_div_arr[17]=[['1,2,3,4','mageb-grid mageb-two-col-2']];
        //$pattern_div_arr[19]=array(array('0,3,4','mageb-grid mageb-two-col-1'),array('1,2,5','mageb-grid mageb-two-col-2'));
        //$pattern_div_arr[20]=array(array('0,1,4','mageb-grid mageb-two-col-1'),array('2,3,5','mageb-grid mageb-two-col-2'));
        $pattern_div_arr[21]=[['0,1,2,3','mageb-grid mageb-two-col-1'],['4','mageb-grid mageb-two-col-2'],['5','mageb-grid mageb-three-col-3']];
        $pattern_div_arr[26]=[['0,1','mageb-grid mageb-three-col-1'],['2','mageb-grid mageb-three-col-2'],['3,4','mageb-grid mageb-three-col-3']];
        $pattern_div_arr[28]=[['0','mageb-grid mageb-three-col-1'],['1,2','mageb-grid mageb-three-col-2'],['3,4','mageb-grid mageb-three-col-3']];
		//$pattern_div_arr[32]=[['1,2,3,4,5','mageb-grid mageb-two-col-2']];
		$pattern_div_arr[32]=[['0,1,2,3','mageb-grid mageb-two-col-1'],['4','mageb-grid mageb-two-col-2']];
		
        return $pattern_div_arr;
    }
    public function resizeImg($fileName, $width, $height)
    {
        $dir = "resized";
        $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                    $advertiseDir = 'magebees_advertisement';
                    $mediaDir->create($advertiseDir);
                    $mediaDir->changePermissions($advertiseDir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
                    $advertiseDir = $mediaDir->getAbsolutePath($advertiseDir);
			         chmod($advertiseDir, 0755);
                    $absPath = $advertiseDir.$fileName;
                    $imageResized = $advertiseDir."/".$dir.$fileName;
        if ($width != '') {
            if (file_exists($imageResized)) {
                unlink($imageResized);
            }
            $imageResize = $this->_imageFactory->create();
            $imageResize->open($absPath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(false);
            $imageResize->resize($width, $height);
            $dest = $imageResized ;
            $imageResize->save($dest);
        }
                    return true;
    }
	 public function getAdvertiseImagedetail($adv_id)
    {
        $adv_model=$this->advertisementimagesFactory->create();
        $collection = $adv_model->getCollection()->addFieldToFilter('advertisement_id', $adv_id);
        return $collection;
    }
    public function getAdvertiseBlockdetail($adv_id)
    {
        $adv_model=$this->advertisementinfoFactory->create();
        $collection = $adv_model->getCollection()->addFieldToFilter('advertisement_id', $adv_id);
        $data=$collection->getData();
        return $data;
    }
     //for posh theme
    public function getIdByUniqueCode($code){
        $adv_model=$this->advertisementinfoFactory->create();
        $id = 0;
        $id = $adv_model->load($code,'unique_code')->getId();
        return $id;
   
	}
}
