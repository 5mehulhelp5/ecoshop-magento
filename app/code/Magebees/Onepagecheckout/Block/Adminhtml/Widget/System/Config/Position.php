<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\Widget\System\Config;
class Position extends \Magebees\Onepagecheckout\Block\Adminhtml\Widget\System\Config\ConfigAbstract
{
    protected $_template = 'Magebees_Onepagecheckout::system/config/position.phtml';
    public function isHasPrefixName() {
        $prefixName = $this->_scopeConfig->getValue('customer/address/prefix_show');
        if ($prefixName) {
            return true;
        } else {
            return false;
        }
    }    
    public function isHasMiddleName() {
        $middleName = $this->_scopeConfig->getValue('customer/address/middlename_show');
        if ($middleName) {
            return true;
        } else {
            return false;
        }
    }
    public function isHasSuffixName() {
        $suffix = $this->_scopeConfig->getValue('customer/address/suffix_show');
        if ($suffix) {
            return true;
        } else {
            return false;
        }
    }
    public function isHasVatId() {
        $taxVat = $this->_scopeConfig->getValue('customer/create_account/vat_frontend_visibility');
        if ($taxVat) {
            return true;
        } else {
            return false;
        }
    }
    public function isHasGender() {
        $gender = $this->_scopeConfig->getValue('customer/address/gender_show');
        if ($gender) {
            return true;
        } else {
            return false;
        }
    }
    public function isHasDateofbirth() {
        $dob = $this->_scopeConfig->getValue('customer/address/dob_show');
        if ($dob) {
            return true;
        } else {
            return false;
        }
    }
    public function isHasCompany() {
        $company = $this->_scopeConfig->getValue('customer/address/company_show');
        if ($company) {
            return true;
        } else {
            return false;
        }
    }
    public function isHasFax() {
        $fax = $this->_scopeConfig->getValue('customer/address/fax_show');
        if ($fax) {
            return true;
        } else {
            return false;
        }
    }
    public function getFieldOptions()
    {
        $fieldOptions = array(
            '0'          => __('Null'),
            'firstname'  => __('First Name'),
            'lastname'   => __('Last Name'),
            'street'     => __('Address'),
            'country_id' => __('Country'),
            'region_id'	 => __('State/Province'),
            'city'       => __('City'),
            'postcode'   => __('Zip/Postal Code'),
            'telephone'  => __('Telephone')
        );
        if ($this->isHasSuffixName()) {
            $fieldOptions['suffix'] =  __('Suffix Name');
        }
        if ($this->isHasMiddleName()) {
            $fieldOptions['middlename'] =  __('Middle Name');
        }
        if ($this->isHasPrefixName()) {
            $fieldOptions['prefix'] =  __('Prefix Name');
        }
        if ($this->isHasVatId()) {
            $fieldOptions['vat_id'] =  __('Tax/VAT Number');
        }
        if ($this->isHasGender()) {
            $fieldOptions['gender'] =  __('Gender');
        }
        if ($this->isHasDateofbirth()) {
            $fieldOptions['dob'] =  __('Date Of Birth');
        }
        if ($this->isHasCompany()) {
            $fieldOptions['company'] =  __('Company');
        }
        if ($this->isHasFax()) {
            $fieldOptions['fax'] =  __('Fax');
        }
        return $fieldOptions;
    }
    public function getDefaultField($number, $scope, $scopeId)
    {
        return $this->_scopeConfig->getValue('magebees_Onepagecheckout/field_position_management/row_' . $number, $scope, $scopeId);
    }
    public function getFieldEnableBackEnd($number, $scope, $scopeId)
    {
       $configCollection = $this->_scopeConfig->getValue('magebees_Onepagecheckout/field_position_management/row_'.$number);

       if ($configCollection != "") {
            return $configCollection;
        } else {
            return null;
        }
    }
    public function getElementHtmlId($number)
    {
        return 'magebees_Onepagecheckout_field_position_management_row_' . $number;
    }
    public function getElementHtmlName($number)
    {
        return 'groups[field_position_management][fields][row_' . $number . '][value]';
    }
    public function getCheckBoxElementHtmlId($number)
    {
        return 'magebees_Onepagecheckout_field_position_management_row_' . $number . '_inherit';
    }
    public function getCheckBoxElementHtmlName($number)
    {
        return 'groups[field_position_management][fields][row_' . $number . '][inherit]';
    }
}