<?php
namespace Magebees\EmailEditor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class GenerateLess implements ObserverInterface
{

	protected $_stores = array();
	protected $resourceConnection;
    protected $httpRequest;
    protected $dirReader;
    protected $_storeManager;
    protected $_scopeConfig;
    protected $helper;
    protected $_store;

    public function __construct(
       
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magebees\EmailEditor\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
		 \Magento\Store\Model\StoreManagerInterface $storeManager,
		  \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader
    ) {
    
       
        $this->helper = $helper;
        $this->resourceConnection = $resourceConnection;
        $this->httpRequest = $httpRequest;
        $this->dirReader = $dirReader;
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		
		if ($observer->getEvent()->getStore()) {
            $scope = 'stores';
            $scopeId = $observer->getEvent()->getStore();
			$store = $this->_storeManager->getStore($scopeId);
			$store_id = $this->_storeManager->getStore($scopeId)->getId();
			$this->_store[$store->getStoreId()] = $store->getCode() ;
        } elseif ($observer->getEvent()->getWebsite()) {
            $scope = 'websites';
            $scopeId = $observer->getEvent()->getWebsite();
			$store_id = $this->_storeManager->getStore($scopeId)->getId();
			$stores = $this->_storeManager->getWebsite($scopeId)->getStores();
			foreach($stores as $store) {
				$this->_store[$store->getStoreId()] = $store->getCode() ;
			}
        } else {
            $scope = 'default';
            $scopeId = 0;
			$store_id = $this->_storeManager->getStore($scopeId)->getId();
			$stores = $this->_storeManager->getStores();
			foreach($stores as $store) {
				$this->_store[$store->getStoreId()] = $store->getCode() ;
			}
        }
  
       
          
				$this->generateConfigLess();              
        
        
    }
	public function generateConfigLess(){
        $store_id=1;
        $is_ext_enabled=$this->helper->getConfig('emaileditor/setting/enable',$store_id);
           
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl();
		$moduleviewDir=$this->dirReader->getModuleDir('view', 'Magebees_EmailEditor');
                $cssDir=$moduleviewDir.'/frontend/web/css/source';
		  if (!file_exists($cssDir)) {
                    mkdir($cssDir, 0777, true);
                }
		//foreach($this->_store as $store_id=>$store_code){
                $store_id=1;
			   $content = '';			
				
                 $header_bgcolor=$this->helper->getConfig('emaileditor/email_header/header_bgcolor',$store_id);
            $footer_bgcolor=$this->helper->getConfig('emaileditor/email_footer/footer_bgcolor',$store_id);


            $topmenu_color=$this->helper->getConfig('emaileditor/top_menu/text_color',$store_id);
            $topmenu_bgcolor=$this->helper->getConfig('emaileditor/top_menu/bgcolor',$store_id);
            $topmenu_padding=$this->helper->getConfig('emaileditor/top_menu/padding',$store_id);
            $topmenu_item_padding=$this->helper->getConfig('emaileditor/top_menu/item_padding',$store_id);


            $body_wrapper_bgcolor=$this->helper->getConfig('emaileditor/body_content/wrapper_bgcolor',$store_id);
            $body_content_bgcolor=$this->helper->getConfig('emaileditor/body_content/content_bgcolor',$store_id);
            $body_text_color=$this->helper->getConfig('emaileditor/body_content/text_color',$store_id);
            $body_link_color=$this->helper->getConfig('emaileditor/body_content/link_color',$store_id);
            $body_total_section_color=$this->helper->getConfig('emaileditor/body_content/total_section_color',$store_id);


            $h1_text_color=$this->helper->getConfig('emaileditor/h1/text_color',$store_id);    
             $h1_custom_font=$this->helper->getConfig('emaileditor/h1/custom_font',$store_id);
             $h1_font_family=$h1_custom_font;
            $h1_font_weight=$this->helper->getConfig('emaileditor/h1/font_weight',$store_id);
            $h1_font_size=$this->helper->getConfig('emaileditor/h1/font_size',$store_id);
            $h1_font_style=$this->helper->getConfig('emaileditor/h1/font_style',$store_id);
            $h1_line_height=$this->helper->getConfig('emaileditor/h1/line_height',$store_id);
            $h1_letter_space=$this->helper->getConfig('emaileditor/h1/letter_space',$store_id);


            $h2_text_color=$this->helper->getConfig('emaileditor/h2/text_color',$store_id);    
             $h2_custom_font=$this->helper->getConfig('emaileditor/h2/custom_font',$store_id);
             $h2_font_family=$h2_custom_font;
            $h2_font_weight=$this->helper->getConfig('emaileditor/h2/font_weight',$store_id);
            $h2_font_size=$this->helper->getConfig('emaileditor/h2/font_size',$store_id);
            $h2_font_style=$this->helper->getConfig('emaileditor/h2/font_style',$store_id);
            $h2_line_height=$this->helper->getConfig('emaileditor/h2/line_height',$store_id);
            $h2_letter_space=$this->helper->getConfig('emaileditor/h2/letter_space',$store_id);
            
            $h3_text_color=$this->helper->getConfig('emaileditor/h3/text_color',$store_id);    
             $h3_custom_font=$this->helper->getConfig('emaileditor/h3/custom_font',$store_id);
             $h3_font_family=$h3_custom_font;
            $h3_font_weight=$this->helper->getConfig('emaileditor/h3/font_weight',$store_id);
            $h3_font_size=$this->helper->getConfig('emaileditor/h3/font_size',$store_id);
            $h3_font_style=$this->helper->getConfig('emaileditor/h3/font_style',$store_id);
            $h3_line_height=$this->helper->getConfig('emaileditor/h3/line_height',$store_id);
            $h3_letter_space=$this->helper->getConfig('emaileditor/h3/letter_space',$store_id);

             $p_text_color=$this->helper->getConfig('emaileditor/paragraph/text_color',$store_id);           
             $p_custom_font=$this->helper->getConfig('emaileditor/paragraph/custom_font',$store_id);
             $p_font_family=$p_custom_font;
            $p_font_weight=$this->helper->getConfig('emaileditor/paragraph/font_weight',$store_id);
            $p_font_size=$this->helper->getConfig('emaileditor/paragraph/font_size',$store_id);
            $p_font_style=$this->helper->getConfig('emaileditor/paragraph/font_style',$store_id);
            $p_line_height=$this->helper->getConfig('emaileditor/paragraph/line_height',$store_id);
            $p_letter_space=$this->helper->getConfig('emaileditor/paragraph/letter_space',$store_id);


             $btn_text_color=$this->helper->getConfig('emaileditor/button/text_color',$store_id);
            $btn_bgcolor=$this->helper->getConfig('emaileditor/button/bgcolor',$store_id);
            $btn_border_color=$this->helper->getConfig('emaileditor/button/border_color',$store_id);
             $btn_hover_color=$this->helper->getConfig('emaileditor/button/hover_color',$store_id);

            $comment_text_color=$this->helper->getConfig('emaileditor/comment/text_color',$store_id);
            $comment_bgcolor=$this->helper->getConfig('emaileditor/comment/bgcolor',$store_id);


            $light_logo_width=$this->helper->getConfig('emaileditor/light_logo/width',$store_id);
            $default_logo_width=$this->helper->getConfig('design/email/logo_width',$store_id);
            $logo_width=$light_logo_width?$light_logo_width:$default_logo_width;
            
            $light_logo_height=$this->helper->getConfig('emaileditor/light_logo/height',$store_id);
            $default_logo_height=$this->helper->getConfig('design/email/logo_height',$store_id);
            $logo_height=$light_logo_height?$light_logo_height:$default_logo_height;

           if (!$is_ext_enabled) {  
            $header_bgcolor='';
            $footer_bgcolor='';
            $topmenu_color='';
            $topmenu_bgcolor='';
            $topmenu_padding='';
            $topmenu_item_padding='';
            $body_wrapper_bgcolor='';
            $body_content_bgcolor='';
            $body_text_color='';
            $body_link_color='';
            $body_total_section_color=''; 
            $h1_text_color='';          
            $h1_custom_font='';
            $h1_font_family='';
            $h1_font_weight='';
            $h1_font_size='';
            $h1_font_style='';
            $h1_line_height='';
            $h1_letter_space='';


            $h2_text_color='';           
            $h2_custom_font='';
            $h2_font_family='';
            $h2_font_weight='';
            $h2_font_size='';
            $h2_font_style='';
            $h2_line_height='';
            $h2_letter_space='';


            $h3_text_color='';           
            $h3_custom_font='';
            $h3_font_family='';
            $h3_font_weight='';
            $h3_font_size='';
            $h3_font_style='';
            $h3_line_height='';
            $h3_letter_space='';


            $p_text_color='';           
            $p_custom_font='';
            $p_font_family='';
            $p_font_weight='';
            $p_font_size='';
            $p_font_style='';
            $p_line_height='';
            $p_letter_space='';

            $btn_text_color='';
            $btn_bgcolor='';
            $btn_border_color='';
            $btn_hover_color='';

            $comment_text_color='';
            $comment_bgcolor='';

            $logo_width='';
            $logo_height='';

            $model_full_border='';

           }
$content.="@import 'editor.less';".PHP_EOL;


            $content.='@email_header_background-color:'.$header_bgcolor.';'.PHP_EOL;
            $content.='@th_header_bgcolor:'.$header_bgcolor.';'.PHP_EOL;
            
            $content.='@email_footer_background-color:'.$footer_bgcolor.';'.PHP_EOL;

            $content.='@topmenu_color:'.$topmenu_color.' !important;'.PHP_EOL;
            $content.='@topmenu_bgcolor:'.$topmenu_bgcolor.';'.PHP_EOL;
            $content.='@topmenu_padding:'.$topmenu_padding.';'.PHP_EOL;
            $content.='@topmenu_item_padding:'.$topmenu_item_padding.';'.PHP_EOL;

            $content.='@body_wrapper_bgcolor:'.$body_wrapper_bgcolor.';'.PHP_EOL;

            $content.='@body_content_bgcolor:'.$body_content_bgcolor.';'.PHP_EOL;
            $content.='@body_text_color:'.$body_text_color.';'.PHP_EOL;
            $content.='@body_link_color:'.$body_link_color.';'.PHP_EOL;
            $content.='@body_total_section_color:'.$body_total_section_color.';'.PHP_EOL;


            $content.='@h1_font_weight:'.$h1_font_weight.';'.PHP_EOL;
            $content.='@h1_font_size:'.$h1_font_size.';'.PHP_EOL;
            $content.='@h1_font_style:'.$h1_font_style.';'.PHP_EOL;
            $content.='@h1_color:'.$h1_text_color.';'.PHP_EOL;
            $content.='@h1_line_height:'.$h1_line_height.';'.PHP_EOL;
            $content.='@h1_letter_spacing:'.$h1_letter_space.';'.PHP_EOL;
            $content.='@h1_font_family:"'.$h1_font_family.'";'.PHP_EOL;
         //   $content.='@h1_font_family:"Shadows Into Light Two", cursive!important;'.PHP_EOL;
            

             $content.='@h2_font_weight:'.$h2_font_weight.';'.PHP_EOL;
            $content.='@h2_font_size:'.$h2_font_size.';'.PHP_EOL;
            $content.='@h2_font_style:'.$h2_font_style.';'.PHP_EOL;
            $content.='@h2_color:'.$h2_text_color.';'.PHP_EOL;
            $content.='@h2_line_height:'.$h2_line_height.';'.PHP_EOL;
            $content.='@h2_letter_spacing:'.$h2_letter_space.';'.PHP_EOL;
            $content.='@h2_font_family:"'.$h2_font_family.'";'.PHP_EOL;

             $content.='@h3_font_weight:'.$h3_font_weight.';'.PHP_EOL;
            $content.='@h3_font_size:'.$h3_font_size.';'.PHP_EOL;
            $content.='@h3_font_style:'.$h3_font_style.';'.PHP_EOL;
            $content.='@h3_color:'.$h3_text_color.';'.PHP_EOL;
            $content.='@h3_line_height:'.$h3_line_height.';'.PHP_EOL;
            $content.='@h3_letter_spacing:'.$h3_letter_space.';'.PHP_EOL;
            $content.='@h3_font_family:"'.$h3_font_family.'";'.PHP_EOL;

             $content.='@p_font_weight:'.$p_font_weight.';'.PHP_EOL;
            $content.='@p_font_size:'.$p_font_size.';'.PHP_EOL;
            $content.='@p_font_style:'.$p_font_style.';'.PHP_EOL;
            $content.='@p_color:'.$p_text_color.';'.PHP_EOL;
            $content.='@p_line_height:'.$p_line_height.';'.PHP_EOL;
            $content.='@p_letter_spacing:'.$p_letter_space.';'.PHP_EOL;
            $content.='@p_font_family:"'.$p_font_family.'";'.PHP_EOL;

             $content.='@btn_bgcolor:'.$btn_bgcolor.';'.PHP_EOL;
            $content.='@btn_color:'.$btn_text_color.';'.PHP_EOL;
            $content.='@btn_border_color:'.$btn_border_color.';'.PHP_EOL;
            $content.='@btn_hover_color:'.$btn_hover_color.';'.PHP_EOL;

            $content.='@comment_text_color:'.$comment_text_color.';'.PHP_EOL;
            $content.='@comment_bgcolor:'.$comment_bgcolor.';'.PHP_EOL;

            $content.='@logo_width:'.$logo_width.';'.PHP_EOL;
            $content.='@logo_height:'.$logo_height.';'.PHP_EOL;
            $content.='@model_full_border:'.$btn_bgcolor.';'.PHP_EOL;
            
            

			$content.='.header {
            background-color: @email_header_background-color;    
            }'.PHP_EOL;
            $content.='.footer {
            background-color: @email_footer_background-color;   
            }'.PHP_EOL;


           /* $content.='.navigation {
              background-color: @topmenu_bgcolor;
              color: @topmenu_color;
              padding: @topmenu_padding; 
              padding: @topmenu_item_padding;
            }'.PHP_EOL; */


             $content.='.navigation{ text-align: center;
    li{ margin:0 @topmenu_item_padding;
        a{ 
            color: @topmenu_color;
            padding: @topmenu_padding; 
            display: block;
            font-weight: 600 !important;
            text-decoration: none !important;
        }
    }
}'.PHP_EOL; 

$content.='.button tr td{ padding-bottom:0; }
.button{
    a{  
        color: @btn_color;
        border: 1px @btn_border_color;
        display:block; padding:10px;
        text-transform: uppercase;
        font-weight: 600;
        width: 100%;
    }
}'.PHP_EOL; 

$content.='.button a:active, 
.button a:hover, 
.button a:visited { background-color: @btn_hover_color !important; border-color:@btn_hover_color !important; }'.PHP_EOL; 

$content.='.order-totals{
    th,td{
        background-color: @body_total_section_color !important; 
    }
}'.PHP_EOL; 

$content.='.main {
    box-shadow: 0 0 5px #ddd; border:10px solid @model_full_border;
    margin: 20px auto !important;
}'.PHP_EOL; 
            $content.='.wrapper {
              background-color: @body_wrapper_bgcolor;  
            }'.PHP_EOL;
            $content.='body {
              min-height: 100%;
              min-width: 100%; 
            }'.PHP_EOL;

            $content.='.main-content {
              background-color: @body_content_bgcolor;
              color: @body_text_color;  
            }'.PHP_EOL;
            $content.='a {  
              color: @body_link_color;  
            }'.PHP_EOL;
            $content.='.total{  
             background-color: @body_total_section_color;
            }'.PHP_EOL;
            $content.='h1
            {
                font-weight: @h1_font_weight;
                font-size: @h1_font_size;
                font-style: @h1_font_style;
                color: @h1_color;
                line-height: @h1_line_height;
                letter-spacing: @h1_letter_spacing; 
                font-family: @h1_font_family; 
                 
            }'.PHP_EOL;
            $content.='h2
            {
                font-weight: @h2_font_weight;
                font-size: @h2_font_size;
                font-style: @h2_font_style;
                color: @h2_color;
                line-height: @h2_line_height;
                letter-spacing: @h2_letter_spacing; 
                font-family: @h2_font_family;
            }'.PHP_EOL;
            $content.='h3 
            {
             font-weight: @h3_font_weight;
                font-size: @h3_font_size;
                font-style: @h3_font_style;
                color: @h3_color;
                line-height: @h3_line_height;
                letter-spacing: @h3_letter_spacing; 
                font-family: @h3_font_family;
            }'.PHP_EOL;
              $content.='p
            {
                font-weight: @p_font_weight;
                font-size: @p_font_size;
                font-style: @p_font_style;
                color: @p_color;
                line-height: @p_line_height;
                letter-spacing: @p_letter_spacing;  
                font-family: @p_font_family;
            }'.PHP_EOL;            
                 $content.='.button {
              background-color: @btn_bgcolor;
              color: @btn_color;
              border-color: @btn_border_color; 
            }'.PHP_EOL; 
            $content.='.button:hover {
              background-color: @btn_hover_color;  
            }'.PHP_EOL; 

            $content.='.message-info {
                td{
                background-color: @comment_bgcolor;
                color: @comment_text_color; 
                }   
            }'.PHP_EOL; 

            $content.='.logo {
                img{
                width: @logo_width;
                height: @logo_height; 
                }   
            }'.PHP_EOL; 
             $content.='th {
              background: @th_header_bgcolor;  
            }'.PHP_EOL; 

//$css_file_name = "_email_".$store_code.".less";
            $css_file_name = "_email.less";
		    $path_css = $cssDir.'/'.$css_file_name;
            file_put_contents($path_css, $content);
							
			
		// }



	}
}
