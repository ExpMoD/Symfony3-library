<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    public function __construct(ContainerInterface $container, Filesystem $filesystem)
    {
        $this->container = $container;
        $this->filesystem = $filesystem;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('resizeCover', [$this, 'resizeCover']),
            new TwigFilter('resizeImage', [$this, 'resizeImage']),
        ];
    }

    public function resizeImage(string $path, int $width, int $height)
    {
        return $this->generateImage($path, $width, $height);
    }

    public function resizeCover(string $path, int $width, int $height)
    {
        $imagePath = $this->container->getParameter('cover_path') . $path;

        return $this->generateImage($imagePath, $width, $height);
    }

    public function generateImage(string $path, int $width, int $height)
    {
        $style =
            'width: ' . $width . 'px;' .
            'height: ' . $height . 'px;' .
            'background-image: url(' . $path . ');';

        return htmlspecialchars_decode('<div class="image-container" style="' . $style . '"></div>');
    }
}
