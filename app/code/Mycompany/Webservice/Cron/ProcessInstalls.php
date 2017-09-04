<?php
/**
 * ProcessInstalls to implement install cron
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservice
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Cron;

/**
 * @var Mycompany\Webservice\Cron\ProcessInstalls
 * 
 * @author tavant
 *
 */

class ProcessInstalls
{
    
    /**
     * 
     * 
     * @var $_restFactory
     */
    private $_restFactory;
    
    /**
     * 
     * 
     * @var $_logger
     */
    private $_logger;
    
    /**
     * 
     * 
     * @var $store_id
     */
    private $store_id;
    
    
    /**
     * 
     * 
     * @param \Mycompany\Webservice\Model\Adapter\RestFactory $restFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    
    public function __construct(
        \Mycompany\Webservice\Model\Adapter\RestFactory $restFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        )
    {
        $this->_restFactory = $restFactory;
        $this->_logger = $logger;
        $this->store_id =  $storeManager->getStore()->getStoreId();
    }
    
    
    /**
     * 
     * 
     * @return \Mycompany\Webservice\Cron\ProcessInstalls
     */
    
    public function execute()
    {      
        
        $x = $this->_restFactory->create()->InstallProduct();
        $this->_logger->info('Cron Works For Installs');
        
        return $this;
    }
}
