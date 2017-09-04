<?php
namespace Mycompany\Webservice\Model\Adapter;

use Magento\Framework\Exception\StateException;

class Rest
{
    
    public function __construct(
        \Mycompany\Webservice\Model\Adapter\JWT $jwtobj,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductRepository $product_repo,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Action $paction,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Mycompany\Webservice\Helper\Data $service_helper,
        \Mycompany\Webservice\Model\NaylorinstallsFactory $saveFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Mycompany\Webservice\Model\ResourceModel\Naylorinstalls\CollectionFactory  $naylorinstallFactory
        ) {
            
            $this->jwt = $jwtobj;
            $this->config = $scopeConfig;
            $this->_productCollectionFactory = $productCollectionFactory;
            $this->product_repo = $product_repo;
            $this->productFactory = $productFactory;
            $this->paction = $paction;
            $this->categoryFactory = $categoryFactory;
            $this->service_helper = $service_helper;
            $this->naylorinstallFactory = $naylorinstallFactory;
            $this->_saveFactory = $saveFactory;
            $this->config = $scopeConfig;
            $this->_cacheTypeList = $cacheTypeList;
            $this->_cacheFrontendPool = $cacheFrontendPool;
            
    }

  
    public function checkCategoryExists($name){
        $data =  $this->categoryFactory->create()->getCollection()->addAttributeToFilter('name',$name)->getFirstItem()->getData();
        if (empty($data)) {
            echo 0;
        } else {
            echo 1;
        }
    }
    
    
    public function setActiveFeatures($assoc_id='',$sku=''){
        
        if($assoc_id!=0){
            $key = $this->config->getValue('service_config/general/naylor_jwt_key');
            
            if($key == ''){
                $key = "DontAsk";
            }
            
            $jwt=$this->jwt;
            $exp = strtotime('+1 year');
            
            $payload = array("sub"=>"magentoAPI","auth"=>"ROLE_USER","iat"=>time(),"exp"=>$exp);
            
            $token =  $jwt->encode($payload, $key, $algo = 'HS256');
            
            
            
            
            $url = $this->config->getValue('username/general/naylor_url');
            
            if($url == ''){
                $url="http://test.tvmbgigtzm.us-east-1.elasticbeanstalk.com/api/associations/$assoc_id/feature-codes/$sku";
            } else {
               // $url=$url."/api/associations/active-feature-codes/".$assoc_id;
                $url = $url."/api/associations/$assoc_id/feature-codes/$sku";
            }
            
            
            $authorization = "Authorization: Bearer $token";
            $ch = curl_init();
            
            $request_headers = array();
            $request_headers[] = $authorization;
            $request_headers[] = 'Content-Type: text/plain';
            
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,'PURCHASED');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);
            
           $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            
            return json_decode($result);
        } else {
            return array();
        }
    }
    
    
    public function checkInstalled($sku,$assoc_id){
        $collection = $this->naylorinstallFactory->create();        
        $collection = $collection->addFieldToFilter('processed',array('neq' => 1));
        $collection = $collection->addFieldToFilter('sku',array('eq' => $sku));
        $collection = $collection->addFieldToFilter('association_id',array('eq' => $assoc_id));
        $collection = $collection->addFieldToFilter('customer_id',array('eq' => $customer_id));
        foreach($collection as $col){
            
            
        }
        
    }
    
    public function InstallProduct(){
        $x = array();
        $collection = $this->naylorinstallFactory->create();
     
        $collection = $collection->addFieldToFilter('processed',array('neq' => 1));
        
        foreach($collection as $col){
            $id = $col->getId();
            $email = $col->getNaylorEmail();
            $assoc_id = $col->getAssociationId();
            $customer_id = $col->getCustomerId();
            $websiteid = $col->getWebsiteId();
            $store_id = $col->getStore();
            $sku = $col->getSku();
            
            $orders['email'] = $email;
            $orders['assoc_id'] = $assoc_id;
            $orders['customer_id'] = $customer_id;
            $orders['website_id'] = $websiteid;
            $orders['store_id'] = $store_id;
            $orders['sku'] = $sku;
            $orders = $this->service_helper->createOrderWithInvoice($orders);
           // $msg = $this->setActiveFeatures($assoc_id,$sku); 
            
          //  $x['msg'][] = $msg;
           // $x['orders'][] = $orders;
            
            $sf = $this->_saveFactory->create()->load($id);
            $sf->setProcessed(1);
            $sf->setOrderId($orders['order_id']);
           // $sf->setRunResult(json_encode($msg));
            $sf->save();
        }
        
        return $x;
        
    }
    
    public function getProductsBySku($storeid=''){
        if($storeid==''){
            $storeid = 1;
        }
        
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('sku');
        
        foreach($collection as $product){
              
            $sku = strtoupper($product->getSku());
            $product_id = $product->getId();
        
            $key = $this->config->getValue('service_config/general/naylor_jwt_key');
            
            if($key == ''){
                $key = "DontAsk";
            }
            
            $jwt = $this->jwt;
            $exp = strtotime('+1 year');
            
            $payload = array("sub"=>"magentoAPI","auth"=>"ROLE_USER","iat"=>time(),"exp"=>$exp);
            
            $token = $jwt->encode($payload, $key, $algo = 'HS256');
            
            $url = $this->config->getValue('service_config/general/naylor_url');
            
            if($url == ''){
                $url = "http://test.tvmbgigtzm.us-east-1.elasticbeanstalk.com";
            } 

            $url = $url."/api/features?code=".$sku;    
            
            $authorization = "Authorization: Bearer $token";
            
            $ch = curl_init();
            
            $request_headers = array();
            $request_headers[] = $authorization;
            $request_headers[] = 'Content-Type: application/json';
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            
            curl_close($ch);
            $data = json_decode($result);
            
            if(isset($data[0])){
                $x[] = $data[0];
                $display_category = $data[0]->primaryCategory->name;                
                $this->paction->updateAttributes([$product_id], ['display_category' => $display_category], $storeid);
            } else {
                $x[] = array();
            }
            
          }
           
          return $x;
    }
    
}