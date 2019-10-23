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
     * @ORM\Column(type="string")
     */
    private $title;


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
     * @ORM\Column(type="integer")
     */
    private $received_by;

    /**
     * Notifications constructor.
     *
     * @param $isRead
     * @param $actionPerformer
     * @param $isLike
     * @param $isReview
     * @param $isRecommend
     * @param $reviewId
     * @param $listId
     * @param $movieTMDBID
     * @param $receivedBy
     * @param $title
     */
    public function __construct(
         $isRead, $actionPerformer, $isLike, $isReview, $isRecommend, $reviewId, $listId, $movieTMDBID, $receivedBy, $title
    ) {
        $this->is_read = $isRead;
        $this->action_performer = $actionPerformer;
        $this->is_like = $isLike;
        $this->is_review = $isReview;
        $this->is_recommend = $isRecommend;
        $this->review = $reviewId;
        $this->list = $listId;
        $this->movie = $movieTMDBID;
        $this->received_by = $receivedBy;
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
    public function getIsRecommend()
    {
        return $this->is_recommend;
    }

    /**
     * @return mixed
     */
    public function getIsRead()
    {
        return $this->is_read;
    }

    /**
     * @return mixed
     */
    public function getActionPerformer()
    {
        return $this->action_performer;
    }

    /**
     * @return mixed
     */
    public function getIsLike()
    {
        return $this->is_like;
    }

    /**
     * @return mixed
     */
    public function getIsReview()
    {
        return $this->is_review;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getReceivedBy()
    {
        return $this->received_by;
    }

}