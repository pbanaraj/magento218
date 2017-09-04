<?php

/**
 * UpdateDisplayAttribute to implement update display attribute cron job
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservice
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Cron;

class UpdateDisplayAttribute
{
    
    private $rest;
    private $_logger;
    private $store_id;
    
    public function __construct(        
        \Mycompany\Webservice\Model\Adapter\RestFactory $restFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        )
    {
        $this->rest = $restFactory;
        $this->_logger = $logger;
        $this->store_id =  $storeManager->getStore()->getStoreId();
    }
    
   
    public function execute()
    {
        $n = $this->rest->create()->getProductsBySku($this->store_id);
        $this->_logger->info('Cron Works');
               
       return $this;
    }
}