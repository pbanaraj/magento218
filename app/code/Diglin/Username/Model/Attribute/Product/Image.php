<?php
namespace   Diglin\Username\Model\Attribute\Product;

class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    
    protected $_uploaderFactory;
    protected $_filesystem;
    protected $_fileUploaderFactory;
    protected $_logger;
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
        ) {
            $this->_filesystem = $filesystem;
            $this->_fileUploaderFactory = $fileUploaderFactory;
            $this->_logger = $logger;
    }
    
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName() . '_additional_data');
        
        if (empty($value) && empty($_FILES)) {
            return $this;
        }
        
        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }
        
        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
            )->getAbsolutePath(
                'catalog/product/'
                );
            
            try {
                
                $uploader = $this->_fileUploaderFactory->create(['fileId' => $this->getAttribute()->getName()]);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $result = $uploader->save($path);
                
                $object->setData($this->getAttribute()->getName(), $result['file']);
                $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            } catch (\Exception $e) {
                if ($e->getCode() != \Magento\MediaStorage\Model\File\Uploader::TMP_NAME_EMPTY) {
                    $this->_logger->critical($e);
                }
            }
            return $this;
    }
}