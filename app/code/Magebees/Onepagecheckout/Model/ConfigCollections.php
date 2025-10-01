<?php
namespace Magebees\Onepagecheckout\Model;
class ConfigCollections implements \Magento\Checkout\Model\ConfigProviderInterface
{
    protected $_configHelper;
    protected $_magebeesHelper;
    protected $objectmanager;
	
    public function __construct(
		\Magebees\Onepagecheckout\Helper\Configurations $configHelper,
		\Magebees\Onepagecheckout\Helper\Data $magebeesHelper,
		\Magento\Framework\ObjectManagerInterface $objectmanager
	
    ) {
        $this->_configHelper = $configHelper;
        $this->_magebeesHelper = $magebeesHelper;
        $this->objectmanager  = $objectmanager;
    }
    public function getConfig()
    {
		$configData['shipping_address'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/shipping_address');
		$configData['product_image_enabled'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/general/product_image_enabled');
		$configData['product_image_width'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/product_image_width');
		$configData['product_image_height'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/product_image_height');
		$configData['country_id'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/country_id');
		$configData['region_id'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/region_id');
		$configData['default_shipping_method'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/default_shipping_method');
		$configData['default_payment_method'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/default_payment_method');
		$configData['enable_terms'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/osp_terms_conditions/enable_terms');
		$configData['term_title'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/osp_terms_conditions/term_title');
		$configData['term_html'] = $this->objectmanager->get('\Magento\Cms\Model\Template\FilterProvider')->getBlockFilter()->filter((string)$this->_configHelper->getConfig('magebees_Onepagecheckout/osp_terms_conditions/term_html'));
		$configData['term_checkboxtitle'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/osp_terms_conditions/term_checkboxtitle');
		$configData['enable_comment'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/add_comment');
		$configData['show_discount'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/general/show_discount');
		$configData['suggest_address'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/general/suggest_address');
        $configData['google_api_key'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/general/google_api_key');
		$configData['enable_delivery_date'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/enable_delivery_date');
        
		$configData['delivery_date_required'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/delivery_date_required');
		
		$configData['deldate_label'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/deldate_label');
        $configData['deldate_available_from'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/deldate_available_from');
        $configData['deldate_available_to'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/deldate_available_to');
		$configData['disabled'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/disabled');
		
		$configData['show_del_time'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/show_del_time');
		
        $configData['hourMin'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/hourMin');
        $configData['hourMax'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/hourMax');
        $configData['format'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/opc_deliverydate/format');
		$configData['enable_vat_validator'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/vat_validator/enable_vat_validator');
		$configData['checkout_page_layout'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/general/checkout_page_layout');
		$configData['show_header_footer'] = (boolean) $this->_configHelper->getConfig('magebees_Onepagecheckout/general/show_header_footer');
		
		$configData['tsEnabled'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/trust_seals/enabled');
		$configData['tsLabel'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/trust_seals/label');
		$configData['tsText'] = $this->_configHelper->getConfig('magebees_Onepagecheckout/trust_seals/text');
		$bvalue = $this->_configHelper->getConfig('magebees_Onepagecheckout/trust_seals/badges');
		$configData['tsBadges'] = $bvalue ? $this->_magebeesHelper->mbunserialize($bvalue) : [];
		$noday = 0;
        if($configData['disabled'] == -1) {
            $noday = 1;
        }
		$configData['noday'] = $noday;
		$configData['company_show'] = $this->_configHelper->getConfig('customer/address/company_show');
		$configData['telephoneval'] = $this->_configHelper->getConfig('customer/address/telephone_show');
	
		/* Attachment Code */
		$checkoutSession = $this->objectmanager->create('\Magento\Checkout\Model\Session');
        // Get system config value from helper
        $isEnabled = $this->_magebeesHelper->isEnabled();
        $isEnabledOrdercomment = $this->_magebeesHelper->getOrdercomment();
        $isEnabledFileupload = $this->_magebeesHelper->getOrderfileupload();
        $Fileuploadstatus = $this->_magebeesHelper->getOrderfileuploadstatus();
        $Ordercommentsstatus = $this->_magebeesHelper->getOrdercommentsstatus();
        $Fileuploadvalue = $this->_magebeesHelper->getOrderfileuploadvalue();
        $Ordercommenttitle = $this->_magebeesHelper->getOrdercommenttitle();
        $Ordercommenttexttitle = $this->_magebeesHelper
        ->getOrdercommenttexttitle();
        $Orderfiletexttitle = $this->_magebeesHelper->getOrderfiletexttitle();
        $Ordercommentbaseurl = $this->_magebeesHelper->getBaseurlordercomment();
        $ordercommentdelete = $this->_magebeesHelper->getOrdercommentdelete();
        $Orderfiledelete = $this->_magebeesHelper->getOrderfiledelete();
        $OrdercommentField = $this->_magebeesHelper->getOrdercommentField();
        $OrdercommentFile = $this->_magebeesHelper->getOrdercommentFile();
        $Order_comments_file_type = $this->_magebeesHelper->getOrdercommentFiletype();
		$NumberofAttachment = $this->_magebeesHelper->getNumberofAttachment();

        $configData['enabled'] = $isEnabled;
        $configData['enabledordercomment'] = $isEnabledOrdercomment;
        $configData['enabledfileupload'] = $isEnabledFileupload;
        $configData['fileuploadstatus'] = $Fileuploadstatus;
        $configData['ordercommentsstatus'] = $Ordercommentsstatus;
        $configData['fileuploadvalue'] = $Fileuploadvalue;
        $configData['ordercommenttitle'] = $Ordercommenttitle;
        $configData['ordercommenttexttitle'] = $Ordercommenttexttitle;
        $configData['orderfiletexttitle'] = $Orderfiletexttitle;
        $configData['ordercommentbaseurl'] = $Ordercommentbaseurl;
        $configData['ordercommentfield'] = $OrdercommentField;
        $configData['ordercommentfieldno'] = $OrdercommentField;
        $configData['ordercommentfile'] = $OrdercommentFile;
        $configData['ordercommentfileno'] = $OrdercommentFile;
        $configData['ordercommentdelete'] = $ordercommentdelete;
        $configData['orderfiledelete'] = $Orderfiledelete;
        $configData['getordercommentstext'] = $checkoutSession->getOrderCommentstext();
        $configData['order_comments_file_type'] = "Allow file : ".$Order_comments_file_type;
		$configData['numberofattachment'] = $NumberofAttachment;

        if ($checkoutSession->getFileuploadvaluestatus()) {
            $Fileuploadvaluestatus = $checkoutSession->getFileuploadvaluestatus();
            if ($configData['fileuploadvalue'] == "" && $Fileuploadvaluestatus == 1) {
                    $configData['fileuploadvaluestatus'] = "Disallowed file type";
            } else {
                    $configData['fileuploadvaluestatus'] = "";
            }
        }
		/* Attachment Code End */
        return $configData;
    }    
}