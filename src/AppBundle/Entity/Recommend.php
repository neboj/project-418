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
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $movie;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * Recommend constructor.
     * @param $receivedBy
     * @param $sentBy
     * @param $isSeen
     * @param $createdAt
     * @param $movie
     * @param $title
     */
    public function __construct($receivedBy, $sentBy, $isSeen, $createdAt, $movie, $title) {
        $this->received_by = $receivedBy;
        $this->sent_by = $sentBy;
        $this->is_seen = $isSeen;
        $this->created_at = $createdAt;
        $this->movie = $movie;
        $this->title = $title;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getReceivedBy()
    {
        return $this->received_by;
    }

    /**
     * @return mixed
     */
    public function getSentBy()
    {
        return $this->sent_by;
    }

    /**
     * @return mixed
     */
    public function getIsSeen()
    {
        return $this->is_seen;
    }

    /**
     * @return mixed
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

}