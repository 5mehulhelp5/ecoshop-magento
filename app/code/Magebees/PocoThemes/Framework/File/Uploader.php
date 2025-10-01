<?php
namespace Magebees\PocoThemes\Framework\File;
use Magento\Framework\Validation\ValidationException;
class Uploader extends \Magento\MediaStorage\Model\File\Uploader
{

        protected function _validateFile()
        {
            if ($this->_fileExists === false) {
                return;
            }
            /// You can also add your allow File types in array and check in_array
            if (!$this->getFileExtension() == 'svg') {
                //is file extension allowed
                if (!$this->checkAllowedExtension($this->getFileExtension())) {
                    throw new ValidationException(__('Disallowed file type.'));
                }
            }
            //run validate callbacks
            foreach ($this->_validateCallbacks as $params) {
                if (is_object($params['object'])
                    && method_exists($params['object'], $params['method'])
                    && is_callable([$params['object'], $params['method']])
                ) {
                    $params['object']->{$params['method']}($this->_file['tmp_name']);
                }
            }
        }
}