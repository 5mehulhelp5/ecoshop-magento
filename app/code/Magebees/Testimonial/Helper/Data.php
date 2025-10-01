<?php

namespace Magebees\Testimonial\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Captcha image model
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * Captcha fonts path
     */
    const XML_PATH_CAPTCHA_FONTS = 'captcha/fonts';

    /**
     * @var Filesystem
     */
    protected $_filesystem;
protected $_objectManager;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

   // protected $_scopeConfig;
    
    protected $_sessionManager;
    protected $_imageFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param \Magento\Captcha\Model\CaptchaFactory $factory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        //\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        Filesystem $filesystem,
		 \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Backend\Model\Session $sessionManager
    ) {
        //$this->_scopeConfig = $scopeConfig;
		$this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_sessionManager = $sessionManager;
		 $this->_imageFactory = $imageFactory;
        parent::__construct($context);
    }

    /**
     * Get list of available fonts.
     *
     * Return format:
     * [['arial'] => ['label' => 'Arial', 'path' => '/www/magento/fonts/arial.ttf']]
     *
     * @return array
     */
    public function getCaptchaFonts()
    {
        $fontsConfig = $this->scopeConfig->getValue(\Magento\Captcha\Helper\Data::XML_PATH_CAPTCHA_FONTS, 'default');
        $fonts = [];
        if ($fontsConfig) {
            $libDir = $this->_filesystem->getDirectoryRead(DirectoryList::LIB_INTERNAL);
            foreach ($fontsConfig as $fontName => $fontConfig) {
                $fonts[$fontName] = [
                    'label' => $fontConfig['label'],
                    'path' => $libDir->getAbsolutePath($fontConfig['path']),
                ];
            }
        }
        return $fonts;
    }

    /**
     * Get captcha image directory
     *
     * @param mixed $website
     * @return string
     */
    public function getImgDir($website = null)
    {
        $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $captchaDir = '/testimonialcaptcha/' . $this->_getWebsiteCode($website);
        $mediaDir->create($captchaDir);
        $mediaDir->changePermissions($captchaDir, DriverInterface::WRITEABLE_DIRECTORY_MODE);

        return $mediaDir->getAbsolutePath($captchaDir) . '/';
    }

    /**
     * Get website code
     *
     * @param mixed $website
     * @return string
     */
    protected function _getWebsiteCode($website = null)
    {
        return $this->_storeManager->getWebsite($website)->getCode();
    }

    /**
     * Get captcha image base URL
     *
     * @param mixed $website
     * @return string
     */
    public function getImgUrl($website = null)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            DirectoryList::MEDIA
        ) . 'testimonialcaptcha' . '/' . $this->_getWebsiteCode(
            $website
        ) . '/';
    }
    
    public function createCaptchaImage()
    {
        $word="";
        $image = imagecreatetruecolor(130, 50);
                
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 200, 50, $background_color);
        $line_color = imagecolorallocate($image, 64, 64, 64);
                
        for ($i=0; $i<10; $i++) {
            imageline($image, 0, rand()%50, 200, rand()%50, $line_color);
        }
        
        $pixel_color = imagecolorallocate($image, 0, 0, 255);
        for ($i=0; $i<1000; $i++) {
            imagesetpixel($image, rand()%200, rand()%50, $pixel_color);
        }
        
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $len = strlen($letters);
        $letter = $letters[rand(0, $len-1)];
        $fonts = $this->getCaptchaFonts();
        $font = $fonts['linlibertine']['path'];
        $text_color = imagecolorallocate($image, 0, 0, 0);

        for ($i = 0; $i< 4; $i++) {
            $letter = $letters[rand(0, $len-1)];
            imagettftext($image, 25, 0, 5+($i*32), 38, $text_color, $font, $letter);
            $word.=$letter;
        }
                        
        $this->_sessionManager->setTestimonialCaptcha($word);//save captcha to session
        
        $path = $this->getImgDir(); //Directory path of captcha image
        chmod($path, 0775);           
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
                
        $random = rand();
        $image_name = "captcha-".$random.".png";
        
        imagepng($image, $path."/".$image_name);//captcha imge at specified directory
        chmod($path."/".$image_name, 0664);
        return $image_name;
    }
	public function checkImgExist($image,$width,$height)
	{
		$mediaUrlDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
		$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
		$resize_img_dir=$mediaDirectory->getAbsolutePath('testimonial/images/thumbnails');
		$upload_img_dir=$mediaDirectory->getAbsolutePath('testimonial/images');
		 if (file_exists($resize_img_dir)) {
			 chmod($resize_img_dir, 0775);
		 }
		if (file_exists($upload_img_dir)) {
			chmod($upload_img_dir, 0775);  
		}
		 
		$resize_img_path=$resize_img_dir.$image;
		$upload_img_path=$upload_img_dir.$image;
		$resize_img_url=$mediaUrlDirectory.'testimonial/images/thumbnails'.$image;
		$upload_img_url=$mediaUrlDirectory.'testimonial/images'.$image;
		 if(file_exists($resize_img_path))
		 {
			 return $resize_img_url;
		 }
		else
		{
			 $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                        $testimonialDir = '/testimonial/images';
			 			$dir = "thumbnails";
                        $mediaDir->create($testimonialDir);
                        $mediaDir->changePermissions($testimonialDir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
                        $testimonialDir = $mediaDir->getAbsolutePath($testimonialDir);
                        $absPath = $testimonialDir.$image;
                        $imageResized = $testimonialDir."/".$dir.$image;
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
			return $resize_img_url;
		}
		
	}
}
