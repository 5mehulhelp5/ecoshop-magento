<?php
namespace Magebees\Productlabel\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;

class Remove extends AbstractHelper
{

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $rootWrite;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $rootRead;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
	
	
	protected $_dir;
	

    const DEFAULT_FILE_PERMISSIONS = 0666;
    const DEFAULT_DIR_PERMISSIONS = 0777;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		DirectoryList $dir,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);

        $this->filesystem = $filesystem;
		$this->_dir = $dir;
        $this->rootWrite = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->rootRead = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    public function removeFolder()
    {
        $app_path =  $this->_dir->getPath('app');
		$folder = array();
		$folder[0] = $app_path."/code/Magebees/Productlabel/Model/Observer";
		$folder[1] = $app_path."/code/Magebees/Productlabel/etc/frontend";
		
		foreach($folder as $folder){
			if(file_exists ($folder)){
				$from = $this->rootRead->getRelativePath($folder);
				$this->removeFilesFrom($from);
			}
		}
    }

    public function removeFilesFrom($fromPath)
    {
		$files = $this->rootRead->readRecursively($fromPath);
		array_unshift($files, $fromPath);
        foreach ($files as $file) {
            if ($this->rootRead->isExist($file)) {
				if ($this->rootRead->isDirectory($file)) {
					$this->rootWrite->changePermissions(
						$file,
						self::DEFAULT_DIR_PERMISSIONS
					);
					$this->rootWrite->delete($file);
				}
			}
        }
	}
   
}
