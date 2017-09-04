<?php
namespace Mycompany\Webservice\Model\Adapter;

use Magento\Framework\Exception\StateException;

class Oauth2 
{
     
   const TOKEN_ENDPOINT  = 'http://naylor.magento2.com/rest/V1/integration/admin/token';
   // const TOKEN_ENDPOINT  = "http://connect.qa.naylor.com/rest/V1/integration/admin/token";
 
    public function __construct(        
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        if (empty($clientId)) {
           //  $clientId = $this->config->getValue('marketplace/config/client_id');
        }

        if (empty($clientSecret)) {
          //  $clientSecret = $this->config->getValue('marketplace/config/client_id));
        }
     
    }

    public function getRedirectUri()
    {
           $resultRedirect = $this->resultRedirectFactory->create();
           $resultRedirect->setPath("marketplaceauth/account/login");
           return $resultRedirect; 
    }
    
    public function executeRequest($url, $parameters = array(), $http_method = 'POST', array $http_headers = null)
    {
        
        if (is_array($http_headers)) {
            $request_headers = array();
            foreach($http_headers as $key => $parsed_urlvalue) {
                $request_headers[] = "$key: $parsed_urlvalue";
            }
            
        }
        
        $parameters = json_encode($parameters);
        
        $ch = curl_init();
        
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 360);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$parameters);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if ($curl_error = curl_error($ch)) {
           
            throw new StateException(__(json_encode($curl_error)));
        } else {
            $json_decode = json_decode($result, true);
        }
        curl_close($ch);
        
        return array(
            'result' => (null === $json_decode) ? $result : $json_decode,
            'code' => $http_code,
            'content_type' => $content_type
        );
    }
}


