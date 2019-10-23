<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 26/12/2017
 * Time: 13:25
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsersListRepository")
 * @ORM\Table(name="users_list")
 */
class UsersList
{
    const LIST_STATE_PUBLIC = "public";
    const LIST_STATE_PRIVATE = "private";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UsersListItem",mappedBy="userslist")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_private;

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
    public function getName()
    {
        return $this->name;
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
    public function getIsPrivate()
    {
        return $this->is_private;
    }

}