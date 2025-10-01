<?php
namespace Magebees\Testimonial\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class ManageConfig implements ObserverInterface
{
    
    protected $testimonialCollection;
    protected $scopeConfig;
    protected $testimonialBlock;
    protected $_imageFactory;
    protected $_directory;
    protected $_storeManager;
    protected $resourceConnection;
    protected $httpRequest;
    protected $_filesystem;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magebees\Testimonial\Block\Testimonial $testimonialBlock,
        \Magebees\Testimonial\Model\Testimonialcollection $testimonialCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->_filesystem = $filesystem;
        $this->httpRequest = $httpRequest;
        $this->resourceConnection = $resourceConnection;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->testimonialCollection = $testimonialCollection;
        $this->testimonialBlock = $testimonialBlock;
    }
     
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $config=$this->testimonialBlock->getConfig();
        
        if ($config['enable']) {
            $dir = "thumbnails";
            $postdata=$this->httpRequest->getPost()->toArray();
            
            if (isset($postdata['groups']['frontend_settings']['fields'])) {
                $frontend_setting=$postdata['groups']['frontend_settings']['fields'];
                
                if (isset($frontend_setting['profile_img_width']['value'])) {
                    $width=$frontend_setting['profile_img_width']['value'];
                } else {
                    $data=$observer->getEvent()->getData();
                    
                    if ($data['website']) {
                        $default=$this->scopeConfig->getValue('testimonial/frontend_settings');
                        $width=$default['profile_img_width'];
                    } elseif ($data['store']) {
                        $website_id=$this->_storeManager->getStore()->getWebsiteId();
                        $conn = $this->resourceConnection->getConnection();
                        $select = $conn->select()
                              ->from(
                                  ['config' => 'core_config_data']
                              )
                              ->where('config.scope=?', 'websites')
                              ->where('config.scope_id=?', $website_id)
                              ->where('config.path=?', 'testimonial/frontend_settings/profile_img_width');
                        $data = $conn->fetchAll($select);
                        if (isset($data['0'])) {
                            $width=$data['0']['value'];
                        } else {
                            $default=$this->scopeConfig->getValue('testimonial/frontend_settings');
                            $width=$default['profile_img_width'];
                        }
                    }
                }
                if (isset($frontend_setting['profile_img_height']['value'])) {
                    $height=$frontend_setting['profile_img_height']['value'];
                } else {
                    $data=$observer->getEvent()->getData();
                    if ($data['website']) {
                        $default=$this->scopeConfig->getValue('testimonial/frontend_settings');
                        $height=$default['profile_img_height'];
                    } elseif ($data['store']) {
                        $website_id=$this->_storeManager->getStore()->getWebsiteId();
                        $conn = $this->resourceConnection->getConnection();
                        $select = $conn->select()
                              ->from(
                                  ['config' => 'core_config_data']
                              )
                              ->where('config.scope=?', 'websites')
                              ->where('config.scope_id=?', $website_id)
                              ->where('config.path=?', 'testimonial/frontend_settings/profile_img_height');
                        $data = $conn->fetchAll($select);
                        
                        if (isset($data['0'])) {
                            $height=$data['0']['value'];
                        } else {
                            $default=$this->scopeConfig->getValue('testimonial/frontend_settings');
                            $height=$default['profile_img_height'];
                        }
                    }
                }
                $collection = $this->testimonialCollection->getCollection();
                foreach ($collection as $testimonial_data) {
                    $fileName = $testimonial_data['image'];
                    if ($fileName != '') {
                        $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                        $testimonialDir = '/testimonial/images';
                        $mediaDir->create($testimonialDir);
                        $mediaDir->changePermissions($testimonialDir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
                        $testimonialDir = $mediaDir->getAbsolutePath($testimonialDir);
                        $absPath = $testimonialDir.$fileName;
                        $imageResized = $testimonialDir."/".$dir.$fileName;
                        if ($width != '') {
                            if (file_exists($imageResized)) {
                                unlink($imageResized);
                            }
                            $imageResize = $this->_imageFactory->create();
                            $imageResize->open($absPath);
                            $imageResize->constrainOnly(true);
                            $imageResize->keepTransparency(true);
                            $imageResize->keepFrame(false);
                            $imageResize->keepAspectRatio(true);
                            $imageResize->resize($width, $height);
                            $dest = $imageResized ;
                            $imageResize->save($dest);
                        }
                    }
                }
            
                return true;
            }
        }
        return true;
    }
}
