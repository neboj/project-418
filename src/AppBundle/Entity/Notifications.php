<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 19/1/2018
 * Time: 14:12
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationsRepository")
 * @ORM\Table(name="notifications")
 */
class Notifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */private $id;
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_read;
    /**
     * @ORM\Column(type="integer")
     */
    private $action_performer;
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_like;
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_review;
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_recommend;

    /**
     * @return mixed
     */
    public function getisRecommend()
    {
        return $this->is_recommend;
    }

    /**
     * @param mixed $is_recommend
     */
    public function setIsRecommend($is_recommend)
    {
        $this->is_recommend = $is_recommend;
    }

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
    public function getisRead()
    {
        return $this->is_read;
    }

    /**
     * @param mixed $is_read
     */
    public function setIsRead($is_read)
    {
        $this->is_read = $is_read;
    }

    /**
     * @return mixed
     */
    public function getActionPerformer()
    {
        return $this->action_performer;
    }

    /**
     * @param mixed $action_performer
     */
    public function setActionPerformer($action_performer)
    {
        $this->action_performer = $action_performer;
    }

    /**
     * @return mixed
     */
    public function getisLike()
    {
        return $this->is_like;
    }

    /**
     * @param mixed $is_like
     */
    public function setIsLike($is_like)
    {
        $this->is_like = $is_like;
    }

    /**
     * @return mixed
     */
    public function getisReview()
    {
        return $this->is_review;
    }

    /**
     * @param mixed $is_review
     */
    public function setIsReview($is_review)
    {
        $this->is_review = $is_review;
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
     * @ORM\Column(type="integer")
     */
    private $received_by;

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

}