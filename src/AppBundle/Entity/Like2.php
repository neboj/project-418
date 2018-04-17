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
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $state;

    /**
     * @return mixed
     */
    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * @return mixed
     */
    public function getisComment()
    {
        return $this->is_comment;
    }

    /**
     * @param mixed $is_comment
     */
    public function setIsComment($is_comment)
    {
        $this->is_comment = $is_comment;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }


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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $review_id;

    /**
     * @return mixed
     */
    public function getReviewId()
    {
        return $this->review_id;
    }

    /**
     * @param mixed $review_id
     */
    public function setReviewId($review_id)
    {
        $this->review_id = $review_id;
    }



    /**
     * @ORM\Column(type="boolean")
     */
    private $is_review;

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

}