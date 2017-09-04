<?php
/**
 *  Naylorlogins Model to implement customer login
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Model\ResourceModel;

class Naylorinstalls extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var Mycompany\Webservice\Model\ResourceModel\Naylorlogins
     * 
     * {@inheritDoc}
     * @see \Magento\Framework\Model\ResourceModel\AbstractResource::_construct()
     */
    
    protected function _construct()
    {
        $this->_init('naylorinstalls', 'id');
    }
}
