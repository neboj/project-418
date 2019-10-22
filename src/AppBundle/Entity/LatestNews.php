<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 11/1/2018
 * Time: 17:13
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LatestNewsRepository")
 * @ORM\Table(name="latest_news")
 */
class LatestNews
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

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
    public function getActionPerformer()
    {
        return $this->actionPerformer;
    }

    /**
     * @return mixed
     */
    public function getisLike()
    {
        return $this->isLike;
    }

    /**
     * @return mixed
     */
    public function getisReview()
    {
        return $this->isReview;
    }

    /**
     * @return mixed
     */
    public function getisAdd()
    {
        return $this->isAdd;
    }

    /**
     * @return mixed
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->list;
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
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $actionPerformer;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isLike;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isReview;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdd;
    /**
     * @ORM\Column(type="integer")
     */
    private $review;
    /**
     * @ORM\Column(type="integer")
     */
    private $list;
    /**
     * @ORM\Column(type="integer")
     */
    private $movie;
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct(
        $actionPerformer, $isLike, $isReview, $isAdd,$review, $list, $movie, $createdAt
    ) {
        $this->actionPerformer = $actionPerformer;
        $this->isLike = $isLike;
        $this->isReview = $isReview;
        $this->isAdd = $isAdd;
        $this->review = $review;
        $this->list = $list;
        $this->movie = $movie;
        $this->created_at  = $createdAt;
    }
}