<?php
namespace Magebees\Blog\Model;
class Import extends \Magento\Framework\Model\AbstractModel
{
	protected $_connect;
	protected $request;
	public function __construct(
        \Magento\Framework\Model\Context $context,
    	\Magento\Framework\App\RequestInterface $request,
    	array $data = []
    ) {
		$this->request = $request;
    	parent::_construct();
    }
	public function getConnection($host,$db_username,$db_password,$db_name)
	{
		$this->_connect = mysqli_connect($host,$db_username,$db_password,$db_name);
		if (mysqli_connect_errno()) {
				throw new \Exception("Failed connect to wordpress database", 1);
			}
		return $this->_connect;
	}
	public function mysqliQuery($sql)
    {
		
        $result = mysqli_query($this->_connect, $sql);
        if (!$result) {
            throw new \Exception(
                __('Mysql error: %1.', mysqli_error($this->_connect))
            );
        }

        return $result;
    }
	
}

