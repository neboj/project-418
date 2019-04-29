<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 4/1/2018
 * Time: 20:15
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReviewRepository")
 * @ORM\Table(name="review")
 */
class Review
{

    /**
     * @ORM\Column(type="integer")
     */
    private $user;
    /**
     * @ORM\Column(type="integer")
     */
    private $movie;
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
     * @ORM\Column(type="string")
     */
    private $review_txt;
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;
    /**
     * @ORM\Column(type="string")
     */
    private $state;

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
    public function getReviewTxt()
    {
        return $this->review_txt;
    }

    /**
     * @param mixed $review_txt
     */
    public function setReviewTxt($review_txt)
    {
        $this->review_txt = $review_txt;
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

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_gif;

    /**
     * @return mixed
     */
    public function getisGif()
    {
        return $this->is_gif;
    }

    /**
     * @param mixed $is_gif
     */
    public function setIsGif($is_gif)
    {
        $this->is_gif = $is_gif;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $gif_url;

    /**
     * @return mixed
     */
    public function getGifUrl()
    {
        return $this->gif_url;
    }

    /**
     * @param mixed $gif_url
     */
    public function setGifUrl($gif_url)
    {
        $this->gif_url = $gif_url;
    }


}