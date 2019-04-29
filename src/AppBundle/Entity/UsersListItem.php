<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 26/12/2017
 * Time: 13:29
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsersListItemRepository")
 * @ORM\Table(name="users_list_item")
 */
class UsersListItem
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $listid;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $listitemid;
    /**
     * @return mixed
     */
    public function getListid()
    {
        return $this->listid;
    }

    /**
     * @param mixed $listid
     */
    public function setListid($listid)
    {
        $this->listid = $listid;
    }

    /**
     * @return mixed
     */
    public function getListitemid()
    {
        return $this->listitemid;
    }

    /**
     * @param mixed $listitemid
     */
    public function setListitemid($listitemid)
    {
        $this->listitemid = $listitemid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    private $name;
    /**
     * @ORM\Column(type="integer")
     */
    private $movie;



}