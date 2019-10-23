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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $movie;

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
     * @ORM\Column(type="string")
     */
    private $gif_url;

    /**
     * Review constructor.
     * @param $user
     * @param $movieTMDBID
     * @param $reviewText
     * @param $createdAt
     * @param $state
     * @param $isGif
     * @param $gifUrl
     */
    public function __construct($user, $movieTMDBID, $reviewText, $createdAt, $state, $isGif, $gifUrl) {
        $this->user = $user;
        $this->movie = $movieTMDBID;
        $this->review_txt = $reviewText;
        $this->created_at = $createdAt;
        $this->state = $state;
        $this->is_gif = $isGif;
        $this->gif_url = $gifUrl;
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
    public function getUser()
    {
        return $this->user;
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
    public function getReviewTxt()
    {
        return $this->review_txt;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_gif;

    /**
     * @return mixed
     */
    public function getIsGif()
    {
        return $this->is_gif;
    }

    /**
     * @return mixed
     */
    public function getGifUrl()
    {
        return $this->gif_url;
    }

}