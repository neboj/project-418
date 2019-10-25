<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 26/12/2017
 * Time: 13:00
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="movie")
 */
class Movie {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $title;
    /**
     * @ORM\Column(type="string")
     */
    private $poster_path;
    /**
     * @ORM\Column(type="float")
     */
    private $vote_average;
    /**
     * @ORM\Column(type="string")
     */
    private $overview;
    /**
     * @ORM\Column(type="string")
     */
    private $genres;
    /**
     * @ORM\Column(type="string")
     */
    private $backdrop_path;


    /**
     * Movie constructor.
     * @param $id int
     * @param $title string
     * @param $poster_path string
     * @param $overview string
     * @param $genres array
     * @param $vote_average int|string
     * @param $backdrop_path
     */
    public function __construct (
        $id, $title, $poster_path, $overview, $genres, $vote_average, $backdrop_path
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->poster_path = $poster_path;
        $this->overview = mb_strimwidth($overview, 0, 254, 'utf-8');
        $this->genres = $genres;
        $this->vote_average = $vote_average;
        $this->backdrop_path = $backdrop_path;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getPosterPath()
    {
        return $this->poster_path;
    }

    /**
     * @return mixed
     */
    public function getVoteAverage()
    {
        return $this->vote_average;
    }

    /**
     * @return mixed
     */
    public function getOverview()
    {
        return $this->overview;
    }

    /**
     * @return mixed
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @return mixed
     */
    public function getBackdropPath()
    {
        return $this->backdrop_path;
    }

}