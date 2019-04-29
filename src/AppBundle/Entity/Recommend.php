<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 25/1/2018
 * Time: 19:01
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecommendRepository")
 * @ORM\Table(name="recommend")
 */
class Recommend
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     */
    private $received_by;
    /**
     * @ORM\Column(type="integer")
     */
    private $sent_by;
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_seen;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getReceivedBy()
    {
        return $this->received_by;
    }

    /**
     * @param mixed $received_by
     */
    public function setReceivedBy($received_by)
    {
        $this->received_by = $received_by;
    }

    /**
     * @return mixed
     */
    public function getSentBy()
    {
        return $this->sent_by;
    }

    /**
     * @param mixed $sent_by
     */
    public function setSentBy($sent_by)
    {
        $this->sent_by = $sent_by;
    }

    /**
     * @return mixed
     */
    public function getisSeen()
    {
        return $this->is_seen;
    }

    /**
     * @param mixed $is_seen
     */
    public function setIsSeen($is_seen)
    {
        $this->is_seen = $is_seen;
    }

    /**
     * @return mixed
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * @param mixed $movie
     */
    public function setMovie($movie)
    {
        $this->movie = $movie;
    }
    /**
     * @ORM\Column(type="integer")
     */
    private $movie;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }
}