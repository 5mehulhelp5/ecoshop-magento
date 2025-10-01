<?php
namespace Magebees\EmailEditor\Plugin;
use Magento\Store\Model\StoreManagerInterface;
class Template 
{
     
    private $serializer;    
    private $helper;
    public function __construct(
        \Magento\Framework\Model\Context $context, 
        \Magebees\EmailEditor\Helper\Data $helper,
        array $data = [],
        ?\Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
       
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
            $this->helper=$helper;
       
    }   
    public function aroundGetVariablesOptionArray(\Magento\Email\Model\Template $subject,callable $proceed,$withGroup = false)
    {        
        $optionArray = $proceed();
        $is_ext_enabled=$this->helper->getConfig('emaileditor/setting/enable');
        if($is_ext_enabled)
        {
            $customblock_val_1='block class="Magento\\\\Cms\\\\Block\\\\Block" area="frontend" block_id="magebees_custom_block_1"';
            $customblock_label_1='Magebees Custom Block 1';
            $variables[$customblock_val_1]=$customblock_label_1;

            $customblock_val_2='block class="Magento\\\\Cms\\\\Block\\\\Block" area="frontend" block_id="magebees_custom_block_2"';
            $customblock_label_2='Magebees Custom Block 2';
            $variables[$customblock_val_2]=$customblock_label_2;

           $socialblock_val='block class="Magento\\\\Cms\\\\Block\\\\Block" area="frontend" block_id="magebees_social_media_block"';       
            $socialblock_label='Magebees Social Media Block';
            $variables[$socialblock_val]=$socialblock_label;

      
          /* $menublock_val='block class="Magento\\\\Theme\\\\Block\\\\Html\\\\Topmenu" area="frontend" template="Magento_Theme::html/topmenu.phtml"'; */
         $menublock_val='block class="Magento\\\\Framework\\\\View\\\\Element\\\\Template" area="frontend" template="Magebees_EmailEditor::email/topmenu.phtml"'; 

            $menublock_label='Magebees Top Menu';
            $variables[$menublock_val]=$menublock_label;

            $darklogo_val='block class="Magento\\\\Framework\\\\View\\\\Element\\\\Template" area="frontend" template="Magebees_EmailEditor::email/darklogo.phtml"';           
            $darklogo_label='Magebees Additional Logo';
            $variables[$darklogo_val]=$darklogo_label;

           /* $lightlogo_val='block class="Magento\\\\Framework\\\\View\\\\Element\\\\Template" area="frontend" template="Magebees_EmailEditor::email/lightlogo.phtml"';           
            $lightlogo_label='Magebees Light Logo';
            $variables[$lightlogo_val]=$lightlogo_label;*/


             if ($variables) {
                foreach ($variables as $value => $label) {
                    $optionArray[] = ['value' => '{{' . $value . '}}', 'label' => __('%1', $label)];
                }
                if ($withGroup) {
                    $optionArray = ['label' => __('Template Variables'), 'value' => $optionArray];
                }
            }
        }
        
        return $optionArray;
    }

}
