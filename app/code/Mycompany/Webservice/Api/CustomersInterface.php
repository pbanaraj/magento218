<?php

/**
 *  CustomersInterface to create customers custom rest api
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservice
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Api;

/**
 * 
 * @var Mycompany\Webservice\Api\CustomersInterface
 * @author tavant
 *
 */

interface CustomersInterface
{
   
    
    /**
     * add attributes from array of data.
     *
     * @api
     * @param mixed $customersData of data to add as as customers attribute
     * @return int The sum of the numbers.
     */
    public function addAttributes($customersData);
    
}