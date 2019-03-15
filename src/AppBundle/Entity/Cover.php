<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="Path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="ActualName", type="string", length=255)
     */
    private $actualName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CreationTime", type="datetime")
     */
    private $creationTime;


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
}
