<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 5/1/2018
 * Time: 23:18
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Like2Repository")
 * @ORM\Table(name="like2")
 */
class Like2
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $comment;

    /**
     * @ORM\Column(type="integer")
     */
    private $post;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_comment;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $review_id;

    /**
     * @ORM\Column(type="string")
     */
    private $state;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_review;

    /**
     * Like2 constructor.
     * @param $created_at
     * @param $is_comment
     * @param $is_review
     * @param $post
     * @param $comment
     * @param $user
     * @param $review_id
     * @param $state
     */
    public function __construct($created_at, $is_comment, $is_review, $post, $comment, $user, $review_id, $state) {
        $this->created_at = $created_at;
        $this->is_comment = $is_comment;
        $this->is_review = $is_review;
        $this->post = $post;
        $this->user = $user;
        $this->comment = $comment;
        $this->review_id = $review_id;
        $this->state = $state;
    }


    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return mixed
     */
    public function getIsComment()
    {
        return $this->is_comment;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
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
    public function getReviewId()
    {
        return $this->review_id;
    }

    /**
     * @return mixed
     */
    public function getIsReview()
    {
        return $this->is_review;
    }

}