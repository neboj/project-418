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
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $movie;

    /**
     * UsersListItem constructor.
     *
     * @param $listID
     * @param $listItemID
     * @param $movieTMDBID
     * @param $title
     */
    public function __construct(
        $listID, $listItemID, $movieTMDBID, $title
    ) {
        $this->listid = $listID;
        $this->listitemid = $listItemID;
        $this->movie = $movieTMDBID;
        $this->name = $title;
    }

    /**
     * @return mixed
     */
    public function getListid()
    {
        return $this->listid;
    }

    /**
     * @return mixed
     */
    public function getListitemid()
    {
        return $this->listitemid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getMovie()
    {
        return $this->movie;
    }

}