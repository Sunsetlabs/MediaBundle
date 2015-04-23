<?php

namespace Sunsetlabs\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sunsetlabs\MediaBundle\Entity\Media;

/**
 * @ORM\Entity
 */
class Image extends Media
{
	public function getThumb()
	{
		return $this->getWebPath();
	}

	public function getType()
	{
	    return 'Image';
	}
}