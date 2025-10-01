<?php
namespace Magebees\RichSnippets\Plugin;

use Magento\Theme\Block\Html\Breadcrumbs as Subject;

class Breadcrumbs
{
    
    public $crumbs;

    public function beforeAddCrumb(Subject $subject, $crumbName, $crumbInfo)
    {
    
        if (!isset($this->crumbs[$crumbName])) {
            $this->crumbs[$crumbName] = $crumbInfo;
        }
        return;
    }
}
