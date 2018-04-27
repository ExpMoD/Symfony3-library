<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="books", schema="library")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookRepository")
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", mappedBy="id")
     * @ORM\JoinColumn(name="cover", referencedColumnName="id")
     */
    private $cover;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\File", mappedBy="id")
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
     * @param Image $cover
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
     * @return Image
     */
    public function getCover()
    {
        return $this->cover;
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
     * @return File
     */
    public function getFile()
    {
        return $this->file;
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
}