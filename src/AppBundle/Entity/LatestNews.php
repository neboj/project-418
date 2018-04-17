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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return mixed
     */
    public function getActionPerformer()
    {
        return $this->actionPerformer;
    }

    /**
     * @param mixed $actionPerformer
     */
    public function setActionPerformer($actionPerformer)
    {
        $this->actionPerformer = $actionPerformer;
    }

    /**
     * @return mixed
     */
    public function getisLike()
    {
        return $this->isLike;
    }

    /**
     * @param mixed $isLike
     */
    public function setIsLike($isLike)
    {
        $this->isLike = $isLike;
    }

    /**
     * @return mixed
     */
    public function getisReview()
    {
        return $this->isReview;
    }

    /**
     * @param mixed $isReview
     */
    public function setIsReview($isReview)
    {
        $this->isReview = $isReview;
    }

    /**
     * @return mixed
     */
    public function getisAdd()
    {
        return $this->isAdd;
    }

    /**
     * @param mixed $isAdd
     */
    public function setIsAdd($isAdd)
    {
        $this->isAdd = $isAdd;
    }

    /**
     * @return mixed
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param mixed $review
     */
    public function setReview($review)
    {
        $this->review = $review;
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param mixed $list
     */
    public function setList($list)
    {
        $this->list = $list;
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
}