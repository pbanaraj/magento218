<?php
/**
 * Sidemenu Block to implement slider in product view page
 * @category Mycompany 
 * @package  Mycompany_Webservice
 * @module   Webservice
 * @author   Tavant Developer
 */

namespace Mycompany\Webservice\Block;

/**
 * 
 * 
 * @var Mycompany\Webservice\Block\Sidemenu
 *
 */

class Sidemenu extends \Magento\Framework\View\Element\Template
{    
    protected $_categoryFactory;
    protected $_category;
    protected $_categoryHelper;
    protected $_categoryRepository;
    protected $httpAdapter;
    protected $jwt;
    protected $loggedinsession;
    
    /**
     * 
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Framework\HTTP\Adapter\Curl $httpAdapter
     * @param \Mycompany\Webservice\Model\Adapter\JWT $jwtobj
     * @param \Magento\Customer\Model\SessionFactory $loggedinsession
     * @param array $data
     */
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,  
        \Magento\Framework\HTTP\Adapter\Curl $httpAdapter,
        \Mycompany\Webservice\Model\Adapter\JWT $jwtobj,
        \Magento\Customer\Model\SessionFactory $loggedinsession,
        \Magento\Store\Model\StoreManagerInterfaceFactory $store,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        /*ScopeConfigInterface $config,*/
        
        array $data = []
    )
    {

        $this->_categoryHelper = $categoryHelper; 
        $this->_categoryFactory = $categoryFactory; 
        $this->_categoryRepository = $categoryRepository; 
        $this->httpAdapter = $httpAdapter; 
        $this->loggedinsession = $loggedinsession;
        $this->jwt = new $jwtobj;
        $this->store = $store;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->config = $context->getScopeConfig();
        parent::__construct($context, $data);
    }
    
    
    public function getCategoryUrlByName($category_name){
        $categoryFactory = $this->_categoryFactory->create();
        $catdata = $categoryFactory->getCollection()->addAttributeToFilter('name',ucfirst($category_name))->getData();
        if(isset($catdata[0]['entity_id']) && $catdata[0]['entity_id']>0){
            $catid = $catdata[0]['entity_id'];
            $title = $categoryFactory->load($catid)->getUrlPath();
            
            $url = $this->store->create()->getStore()->getBaseUrl().$title.'.html';
        } else {
            $url = '#';
        }
        
        return $url;
    }
    
    /**
     * Get category object
     * Using $_categoryFactory
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($categoryId) 
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);        
        return $this->_category;
    }
 
    /**
     * Get category object
     * Using $_categoryRepository
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategoryById($categoryId) 
    {
        return $this->_categoryRepository->get($categoryId);
    }
 
    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection or
     * \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true) 
    {
        return $this->_categoryHelper->getStoreCategories();
    }
    
    /**
     * Get parent category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getParentCategory($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getParentCategory();
        } else {
            return $this->getCategory($categoryId)->getParentCategory();        
        }        
    }
    
    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getParentId();
        } else {
            return $this->getCategory($categoryId)->getParentId();
        }
    }
    
    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getParentIds();
        } else {
            return $this->getCategory($categoryId)->getParentIds();
        }        
    }
    
    /**
     * Get all children categories IDs
     *
     * @param boolean $asArray return result as array instead of comma-separated list of IDs
     * @return array|string
     */
    public function getAllChildren($asArray = false, $categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getAllChildren($asArray);
        } else {
            return $this->getCategory($categoryId)->getAllChildren($asArray);
        }
    }

    public function getAllChildrens($asArray = false, $categoryId = false)
    {
        return $this->getCategory($categoryId)->getAllChildren($asArray);
    }
 
    /**
     * Retrieve children ids comma separated
     *
     * @return string
     */
    public function getChildren($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getChildren();
        } else {
            return $this->getCategory($categoryId)->getChildren();
        }        
    }
    
    function getConfigUrlRedirect(){
        return $key = $this->config->getValue('service_config/general/naylor_config_key');
    }

    
    /**
     * to get product details by assoc_id
     * @see product view page
     * @param string $assoc_id
     * @return mixed|array
     */
    
    public function getProductDetails($assoc_id='',$sku=''){
        $categoryFactory = $this->_categoryFactory->create();
        // $assoc_id = 2267;
        if($assoc_id!=0){
            
            
            $key = $this->config->getValue('service_config/general/naylor_jwt_key');
            
            if($key == ''){
                $key = "DontAsk";
            }
            
            $jwt=$this->jwt;
            $exp = strtotime('+1 year');
            
            $payload = array("sub"=>"magentoAPI","auth"=>"ROLE_USER","iat"=>time(),"exp"=>$exp);
            
            $token =  $jwt->encode($payload, $key, $algo = 'HS256');
            
            $url = $this->config->getValue('service_config/general/naylor_url');
            
            
            if($url == ''){
                $url="http://test.tvmbgigtzm.us-east-1.elasticbeanstalk.com/api/products?association.assocId/".$assoc_id;
            } else {
                // api/associations/active-feature-codes/${association_id}
                $url = $url."/api/products?status=ACTIVE&association.assocId=".$assoc_id;
            }
            
            
            
            
            
            
            
            
            $authorization = "Authorization: Bearer $token";
         
            $ch = curl_init();
            
            $request_headers = array();
            $request_headers[] = $authorization;
            $request_headers[] = 'Content-Type: application/json';
            
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);// curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            
            curl_close($ch);
            $data = json_decode($result);
            
            //print_r($data);
            //die();
            
            $dtt['cc'] = 0;
            $dtt['am'] = 0;
         foreach($data as $dt){
             if($dt->productType == 'CAREERS'){
                 $dtt['cc'] = 1;
             }
                 
             if($dt->productType == 'AMS'){
                 $dtt['am'] = 1;
             }
             
         }
            
       return $dtt;    
    } else {
        $dtt['cc'] = 0;
        $dtt['am'] = 0;
        return $dtt;    
    }
    
    }
    
    /*
     
  public function getProductDetails($assoc_id='',$sku=''){
        $categoryFactory = $this->_categoryFactory->create();
        $assoc_id = 2267;
        if($assoc_id!=0){
           
            
            $key = $this->config->getValue('service_config/general/naylor_jwt_key');
            
            if($key == ''){
                $key = "DontAsk";
            }
            
            $jwt=$this->jwt;
            $exp = strtotime('+1 year');
            
            $payload = array("sub"=>"magentoAPI","auth"=>"ROLE_USER","iat"=>time(),"exp"=>$exp);
            
            $token =  $jwt->encode($payload, $key, $algo = 'HS256');
            
            $url = $this->config->getValue('service_config/general/naylor_url');
            
            
            if($url == ''){
                $url="http://test.tvmbgigtzm.us-east-1.elasticbeanstalk.com/api/products?association.assocId/".$assoc_id;
            } else {      
               // api/associations/active-feature-codes/${association_id}
                $url = $url."/api/associations/active-feature-codes/".$assoc_id;     
            }
            
            
            
            
            
            
            
            
            $authorization = "Authorization: Bearer $token";
           //  return $authorization.'_'.$url;
            $ch = curl_init();
            
            $request_headers = array();
            $request_headers[] = $authorization;
            $request_headers[] = 'Content-Type: application/json';
            
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);// curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
           
            curl_close($ch);
            $data = json_decode($result);
            
            print_r($data);
            die();
            
            foreach($data as $prod){
                $products = array();
                
                $products_display_category = array();
                $products_active = array();
                $products_inactive = array();
                
                $products_display_url = array();
                $products_display_sku = array();
                $products_display_name = array();
                
                foreach($prod->productFeatures  as $product_feature){
                                    
                                     
                    if($sku!=''){
                        
                        if($sku==strtolower($product_feature->feature->code)){
                          
                            $catdata = $categoryFactory->getCollection()->addAttributeToFilter('name',ucfirst($product_feature->feature->primaryCategory->name))->getData();
                            if(isset($catdata[0]['entity_id']) && $catdata[0]['entity_id']>0){
                            $catid = $catdata[0]['entity_id'];
                            $title = $categoryFactory->load($catid)->getUrlPath();
                            
                            $title = $this->store->create()->getStore()->getBaseUrl().$title.'.html';
                            } else {
                                $title = '';
                            }
                            
                      
                        $category['display_category_name'] = $product_feature->feature->primaryCategory->name;
                        $category['display_category_code'] = $product_feature->feature->primaryCategory->code;
                        $category['display_category_status'] = $product_feature->feature->primaryCategory->status;
                        $category['display_category_sku'] = strtolower($product_feature->feature->code);
                        $category['display_category_rcode'] = $title;
                        
                        if($product_feature->feature->status == 'ACTIVE'){
                            $products_active[] = strtolower($product_feature->feature->code);
                        } else {
                            $products_inactive[] = strtolower($product_feature->feature->code);
                        }
                        
                        $products_display_category[] = $category;
                        }
                    } else {
                        $catdata = $categoryFactory->getCollection()->addAttributeToFilter('name',ucfirst($product_feature->feature->primaryCategory->name))->getData();
                        if(isset($catdata[0]['entity_id']) && $catdata[0]['entity_id']>0){
                            $catid = $catdata[0]['entity_id'];
                            $title = $categoryFactory->load($catid)->getUrlPath();
                            
                            $title = $this->store->create()->getStore()->getBaseUrl().$title.'.html';
                        } else {
                            $title = '';
                        }
                        
            
                        $category_sku = $product_feature->feature->code;
                        $category_url = $title;
                        $category_name = $product_feature->feature->primaryCategory->name;
                     
                        
                        if($product_feature->feature->status == 'ACTIVE'){
                            $products_active[] = strtolower($product_feature->feature->code);
                        } else {
                            $products_inactive[] = strtolower($product_feature->feature->code);
                        }
                        

                        $products_display_url[] = $category_url;
                        $products_display_sku[] = $category_sku;
                        $products_display_name[] = $category_name;
                    }
                 
                    
             }
            
        } 
        
        if($sku!=''){
        $results['active'] = $products_active;
        $results['inactive'] = $products_inactive;
        $results['disp_cat'] = $products_display_category;
        } else{
            $results['inactive'] = $products_inactive;
            $results['active'] = $products_active;
            $results['sku'] = $products_display_sku;
            $results['name'] = $products_display_name;
            $results['url'] = $products_display_url;
          
            
            
           
        }
        
        return $results;
        
        
        } else {
            return array();
        }
        
    }
    
    */

    /**
     * to provide array of active features
     * @see grid.phtml
     * @param string $assoc_id
     * @return mixed|array
     */
    
    public function getActiveFeatures($assoc_id=''){
        
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
            $url="http://test.tvmbgigtzm.us-east-1.elasticbeanstalk.com/api/associations/".$assoc_id."/feature-codes?status=ACTIVE&status=PURCHASED";
        } else {
          //  $url=$url."/api/associations/active-feature-codes/".$assoc_id;   
            $url=$url."/api/associations/".$assoc_id."/feature-codes?status=ACTIVE&status=PURCHASED";
        }
        
       
        
        
        
        
        $authorization = "Authorization: Bearer $token";
        $ch = curl_init();
        
        $request_headers = array();
        $request_headers[] = $authorization;
        $request_headers[] = 'Content-Type: application/json';
        
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);// curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
        } else {
            return array();
        }
        
       
        
        
    }
    
    /**
     * to get loggedin users association_id 
     * @see getActiveFeatures
     * @return number
     */
    
    public function getLoggedinUsers(){
        $customerSession = $this->loggedinsession->create();
        if($customerSession->isLoggedIn()){
        $data=$customerSession->getCustomer()->getData();
        if(isset($data['association_id'])){
            return $data['association_id'];
        } else {
            return 0;
        }
        
        } else {
            return 0;
        }
    } 
    
    
}
?>