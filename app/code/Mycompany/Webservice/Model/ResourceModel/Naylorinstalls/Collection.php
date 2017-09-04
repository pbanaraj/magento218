<?php
/**
 * Collection Naylorslogin Resource Model to implement customer login
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */


namespace Mycompany\Webservice\Model\ResourceModel\Naylorinstalls;

/**
 * 
 * @var Mycompany\Webservice\Model\ResourceModel\Naylorlogins\Collection
 * @author tavant
 *
 */

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Mycompany\Webservice\Model\Naylorinstalls',
            'Mycompany\Webservice\Model\ResourceModel\Naylorinstalls');
    }
}
