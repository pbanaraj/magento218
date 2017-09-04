<?php
namespace Diglin\Username\Block;

// use Magento\Framework\App\Config\ScopeConfigInterface;

class Install extends \Magento\Framework\View\Element\Template
{
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\HTTP\Adapter\Curl $httpAdapter,
        \Mycompany\Webservice\Model\Adapter\JWT $jwtobj,
        \Magento\Customer\Model\SessionFactory $loggedinsession,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        /*ScopeConfigInterface $config,*/
        array $data = []
        )
    {
        
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->httpAdapter = $httpAdapter;
        $this->loggedinsession = $loggedinsession;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        
        $this->jwt = new $jwtobj;      
        $this->config = $context->getScopeConfig();
        parent::__construct($context, $data);
    }
    
    
}