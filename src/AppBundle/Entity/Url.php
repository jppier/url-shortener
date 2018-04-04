<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="urls")
 */
class Url
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string")
     */
    private $shortURL;
    
    /**
     * @ORM\Column(type="string")
     */
    private $originalURL;
    
    /**
     * @ORM\Column(type="string")
     */
    private $slug;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $created;
    
    public function setShortURL($url)
    {
        $this->shortURL = $url;
    }
    
    public function getShortURL()
    {
        return $this->shortURL;
    }
    
    public function setOriginalURL($url)
    {
        $this->originalURL = $url;
    }
    
    public function getOriginalURL()
    {
        return $this->originalURL;
    }
    
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function setCreated($timestamp)
    {
        $this->created = $timestamp;
    }
    
    public function getCreated()
    {
        return $this->created;
    }
}