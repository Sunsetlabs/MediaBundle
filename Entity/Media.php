<?php

namespace Sunsetlabs\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"media" = "Media", "image" = "Image"})
 */
class Media
{
	/**
     * Media id.
     *  
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
	protected $id;

    /**
     * Media original name.
     * 
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $originalName;

    /**
     * Media original size.
     * 
     * @ORM\Column(type="decimal", scale=2)
     * @var float
     */
    protected $size;

    /**
     * Media original extension.
     *
     * @ORM\Column(type="string", length=200)
     * @var string
     */
    protected $extension;
	
    /**
     * Path to media.
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
	protected $path;

    /**
     * Media file to upload.
     * 
     * @Assert\File(maxSize="6000000")
     * @var UploadedFile
     */
	protected $file = null;

    /**
     * Media temp file to remove it when object is deleted.
     * @var string
     */
    protected $temp;
    
    /**
     * Related Object unqualified class name.
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $objClass = null;
    
    /**
     * Related Object id.
     * 
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    protected $objId = null;
    
    /**
     * Related Object fully qualified class name.
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     * @var string|null
     */
    protected $objQualifiedClass = null;

    /**
     * Media Position relative to other images of RelatedObject
     *
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $position = 0;

    function __construct($file = null, $object = null) {
        if ($file) {
            $this->setFile($file);
        }
        if ($object) {
            $this->setRelatedObject($object);
        }
    }

    /**
     * Gets id
     * @return int
     */
	public function getId()
	{
		return $this->id;
	}

    /**
     * Sets Media Original Name
     * @param string $name
     * @return Media
     */
    public function setOriginalName($name)
    {
        $this->originalName = $name;
        return $this;
    }

    /**
     * Gets Media Original Name
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Sets Media Size
     * @param float $size
     * @return Media
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Gets Media Size
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets Media extension
     * @param string $extension
     * @return Media
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Gets Media extension
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Sets path
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Gets path
     * @return string
     */
	public function getPath()
	{
		return $this->path;
	}

    /**
     * Sets file
     * @param UploadedFile $file
     * @return Media
     */
	public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        if (isset($this->path)) {
            $this->temp = $this->path;
            $this->path = null;
        }
        return $this;
    }

    /**
     * Gets file
     * @return UploadedFile|null
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Sets Related Object Unqualified Class Name
     * @param string $class
     * @return Media
     */
    public function setObjClass($class)
    {
        $this->objClass = $class;
        return $this;
    }

    /**
     * Gets Related Object Unqualified Class Name
     * @return string
     */
    public function getObjClass()
    {
        return $this->objClass;
    }

    /**
     * Sets Related Object Id
     * @param int $objectId
     * @return Media
     */
    public function setObjId($objectId)
    {
        $this->objId = $objectId;
        return $this;
    }

    /**
     * Gets Related Object Id
     * @return int
     */
    public function getObjId()
    {
        return $this->objId;
    }

    /**
     * Set Related Object Qualified Class Name
     * @param string $class
     * @return Media
     */
    public function setObjQualifiedClass($class)
    {
        $this->objQualifiedClass = $class;
        return $this;
    }

    /**
     * Gets Related Object Qualified Class Name
     * @return string
     */
    public function getObjQualifiedClass()
    {
        return $this->objQualifiedClass;
    }

    /**
     * Sets position
     * @param int $position
     * @return Media
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Gets position
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets Related Object
     * @param mixed $object
     * @return Media
     */
    public function setRelatedObject($object)
    {
        if ($object) {
            $this->setObjId($object->getId());
            $this->setObjQualifiedClass(get_class($object));
            $this->setObjClass(substr($this->getObjQualifiedClass(), strrpos($this->getObjQualifiedClass(), '\\')+1));
        }
        return $this;
    }

    /**
     * Get Formatted size, prettify size and adds unit.
     * 
     * @return string
     */
    public function getFormattedSize()
    {
        $bytes = $this->size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     * Lifecycle event. Called before update/presist
     * of Media. Sets path, orignalName and size if file is set.
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->extension = $this->getFile()->getClientOriginalExtension();
            $this->originalName = $this->getFile()->getClientOriginalName();
            $this->size = $this->getFile()->getClientSize();
            $this->path = $filename.'.'.$this->extension;
        }
    }

    /**
     * Lifecycle event. Called after update/persist of Media.
     * Moves image to proyect.
     * 
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        if (isset($this->temp)) {
            @unlink($this->getUploadRootDir().'/'.$this->temp);
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * Lifecycle event. Called after remove of Media.
     * Delets file.
     * 
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            @unlink($file);
        }
    }

    /**
     * Gets Absolute path to Meda
     * @return string|null
     */
	public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    /**
     * Gets Relative path to Media
     * @return string|null
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : '/'.$this->getUploadDir().'/'.$this->path;
    }

    /**
     * Gets Absolute path to uploade directory
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Gets Relative upload directory
     * @return string
     */
    protected function getUploadDir()
    {
        $dir = 'uploads';
        return null === $this->getObjClass()
            ? $dir
            : $dir . '/' . $this->getObjClass();
    }

    /**
     * Gets the Type of Media
     * @return string
     */
    public function getType()
    {
        return 'Media';
    }

    public function __toString()
    {
        return null === $this->path
            ? $this->getType()
            : $this->getUploadRootDir().'/'.$this->path;
    }
}