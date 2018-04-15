<?php
namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="url_hits")
 */
class UrlHit
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Url
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Url")
     * @ORM\JoinColumn(name="url_id", referencedColumnName="id")
     */
    private $url;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $accessed;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=TRUE)
     */
    private $referrer;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $ip;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UrlHit
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param Url $url
     * @return UrlHit
     */
    public function setUrl(Url $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getAccessed()
    {
        return $this->accessed;
    }

    /**
     * @param DateTime $accessed
     * @return UrlHit
     */
    public function setAccessed($accessed)
    {
        $this->accessed = $accessed;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     * @return UrlHit
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return UrlHit
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }
}
