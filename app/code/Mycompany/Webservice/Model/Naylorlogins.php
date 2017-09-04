<?php
/**
 * Collection Naylorslogin Resource Model to implement custom login
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Model;

/**
 * @var Mycompany\Webservice\Model\Naylorlogins
 * 
 * @author tavant 
 *
 */

class Naylorlogins extends \Magento\Framework\Model\AbstractModel
{
    
   /**
    * 
    * @var unknown
    */
    
    protected $_naylorloginsCollectionFactory;
    
    /**
     * 
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mycompany\Webservice\Model\ResourceModel\Naylorlogins $resource
     * @param \Mycompany\Webservice\Model\ResourceModel\Naylorlogins\Collection $resourceCollection
     */
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,       
        \Mycompany\Webservice\Model\ResourceModel\Naylorlogins $resource,
        \Mycompany\Webservice\Model\ResourceModel\Naylorlogins\Collection $resourceCollection
        ) {
           
            parent::__construct($context, $registry, $resource, $resourceCollection);            
           
    }

   
}
