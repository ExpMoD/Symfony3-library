<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Proxy\Proxy;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Book
 *
 * @ORM\Table(name="books", schema="library")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Book
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
     * @Assert\Length(min="2")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var Cover
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Cover", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="cover", referencedColumnName="id")
     */
    private $cover;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\File", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="file", referencedColumnName="id")
     */
    private $file;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOfReading", type="datetime")
     */
    private $dateOfReading;

    /**
     * @var bool
     *
     * @ORM\Column(name="allowDownloading", type="boolean")
     */
    private $allowDownloading;


    public function __construct()
    {
        $this->dateOfReading = new \DateTime();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Book
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set author.
     *
     * @param string $author
     *
     * @return Book
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set cover.
     *
     * @param Cover $cover
     *
     * @return Book
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover.
     *
     * @return Cover|boolean
     */
    public function getCover()
    {
        return (!is_null($this->cover)) ? $this->cover : false;
    }

    /**
     * Set file.
     *
     * @param File $file
     *
     * @return Book
     */
    public function setFile($file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return File|boolean
     */
    public function getFile()
    {
        return (!is_null($this->file)) ? $this->file : false;
    }

    /**
     * Set dateOfReading.
     *
     * @param \DateTime $dateOfReading
     *
     * @return Book
     */
    public function setDateOfReading($dateOfReading)
    {
        $this->dateOfReading = $dateOfReading;

        return $this;
    }

    /**
     * Get dateOfReading.
     *
     * @return \DateTime
     */
    public function getDateOfReading()
    {
        return $this->dateOfReading;
    }

    /**
     * Set allowDownloading.
     *
     * @param bool $allowDownloading
     *
     * @return Book
     */
    public function setAllowDownloading($allowDownloading)
    {
        $this->allowDownloading = $allowDownloading;

        return $this;
    }

    /**
     * Get allowDownloading.
     *
     * @return bool
     */
    public function getAllowDownloading()
    {
        return $this->allowDownloading;
    }

    /**
     * @ORM\PostLoad()
     */
    public function initializeProxyClasses()
    {
        if ($this->cover instanceof Proxy) {
            $this->cover->__load();
        }

        if ($this->file instanceof Proxy) {
            $this->file->__load();
        }
    }
}
