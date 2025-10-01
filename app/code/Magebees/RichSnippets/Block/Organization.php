<?php

namespace Magebees\RichSnippets\Block;

use Magento\Framework\View\Element\Template;

class Organization extends Template
{
    protected $_scopeConfig;
    protected $_template = 'organization.phtml';
   
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    public function canShowContent()
    {
         $ext_enable=$this->_scopeConfig->getValue('richsnippets/setting/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $organization_enable=$this->_scopeConfig->getValue('richsnippets/organization/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         if ((!$organization_enable)||(!$ext_enable)) {
            return false;
        }
        $base_url=$this->_storeManager->getStore()->getBaseUrl();
        $orgParameters = array(
             'name'        => $this->_scopeConfig->getValue('richsnippets/organization/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'logo_url'        => $this->_scopeConfig->getValue('richsnippets/organization/logo_url',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'url'=>$base_url,
             'description'        => $this->_scopeConfig->getValue('richsnippets/organization/description',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'desc_length'        => $this->_scopeConfig->getValue('richsnippets/organization/desc_length',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'country'        => $this->_scopeConfig->getValue('richsnippets/organization/country',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'region'        => $this->_scopeConfig->getValue('richsnippets/organization/region',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'zipcode'        => $this->_scopeConfig->getValue('richsnippets/organization/zipcode',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'city'        => $this->_scopeConfig->getValue('richsnippets/organization/city',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
             'social_profile_enabled'        => $this->_scopeConfig->getValue('richsnippets/social_profile/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),            

        );
        if (array_filter($orgParameters)) {
            return $orgParameters;
        }

        return false;
    }
    public function getSocialMediaContent()
    {       
        $socialParameters = array();
        $facebook=$this->_scopeConfig->getValue('richsnippets/social_profile/facebook',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $twitter=$this->_scopeConfig->getValue('richsnippets/social_profile/twitter',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $google=$this->_scopeConfig->getValue('richsnippets/social_profile/google',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $instagram=$this->_scopeConfig->getValue('richsnippets/social_profile/instagram',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $youtube=$this->_scopeConfig->getValue('richsnippets/social_profile/youtube',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $linkedin=$this->_scopeConfig->getValue('richsnippets/social_profile/linkedin',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $myspace=$this->_scopeConfig->getValue('richsnippets/social_profile/myspace',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $soundcloud=$this->_scopeConfig->getValue('richsnippets/social_profile/soundcloud',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $pinterest=$this->_scopeConfig->getValue('richsnippets/social_profile/pinterest',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $tumblr=$this->_scopeConfig->getValue('richsnippets/social_profile/tumblr',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($facebook!='')
        {
            $socialParameters[]=$facebook;
        }
        if($twitter!='')
        {
            $socialParameters[]=$twitter;
        }
        if($google!='')
        {
            $socialParameters[]=$google;
        }
        if($instagram!='')
        {
            $socialParameters[]=$instagram;
        }
        if($youtube!='')
        {
            $socialParameters[]=$youtube;
        }
        if($linkedin!='')
        {
            $socialParameters[]=$linkedin;
        }
        if($myspace!='')
        {
            $socialParameters[]=$myspace;
        }
         if($soundcloud!='')
        {
            $socialParameters[]=$soundcloud;
        }
         if($pinterest!='')
        {
            $socialParameters[]=$pinterest;
        }
         if($tumblr!='')
        {
            $socialParameters[]=$tumblr;
        }
         if (array_filter($socialParameters)) {
            return $socialParameters;
        }

        return false;

    }
    public function getContactNumbers()
    {
        $contactParameters=array();
        $sales=$this->_scopeConfig->getValue('richsnippets/organization/sales',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $technical_support=$this->_scopeConfig->getValue('richsnippets/organization/tech_support',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $cust_service=$this->_scopeConfig->getValue('richsnippets/organization/cust_service',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($sales!='')
        {
            $contactParameters['sales']=$sales;
        }
        if($technical_support!='')
        {
            $contactParameters['technical support']=$technical_support;
        }
        if($cust_service!='')
        {
            $contactParameters['customer service']=$cust_service;
        }   
        if (array_filter($contactParameters)) {
            return $contactParameters;
        }

        return false;

    }
}
