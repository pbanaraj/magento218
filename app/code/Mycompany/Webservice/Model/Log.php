<?php

class Mycompany_Webservice_Model_Log
{
    public static function log($message, $level = null, $forceLog = false)
    {
        Mage::log($message, $level, 'github.log', $forceLog);
    }
}
