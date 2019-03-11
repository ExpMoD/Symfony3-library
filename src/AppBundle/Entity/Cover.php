<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * File
 *
 * @ORM\Table(name="covers", schema="library")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoverRepository")
 */
class Cover
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="Path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="ActualName", type="string", length=255)
     */
    private $actualName;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="CreationTime", type="datetime")
     */
    private $creationTime;

    /**
     * @var UploadedFile|null
     * @Assert\Image(
     *     mimeTypes={"image/png", "image/jpg", "image/jpeg"}
     * )
     */
    private $file;


    public function __construct()
    {
        $this->creationTime = new \DateTime();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return Cover
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set actualName.
     *
     * @param string $actualName
     *
     * @return Cover
     */
    public function setActualName($actualName)
    {
        $this->actualName = $actualName;

        return $this;
    }

    /**
     * Get actualName.
     *
     * @return string
     */
    public function getActualName()
    {
        return $this->actualName;
    }

    /**
     * Set creationTime.
     *
     * @param \DateTime $creationTime
     *
     * @return Cover
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Get creationTime.
     *
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    public function isValid()
    {
        return (!empty($this->path) && !empty($this->actualName) && !empty($this->creationTime));
    }

    /**
     * @return UploadedFile|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     * @return Cover
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}
