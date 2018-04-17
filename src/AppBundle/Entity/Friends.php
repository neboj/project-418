<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 19/10/2017
 * Time: 21:05
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FriendsRepository")
 * @ORM\Table(name="friends")
 */
class Friends
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;



    /**
     * @ORM\Column(type="boolean")
     */
    private $is_accepted;

    /**
     * @return mixed
     */
    public function getisAccepted()
    {
        return $this->is_accepted;
    }

    /**
     * @param mixed $is_accepted
     */
    public function setIsAccepted($is_accepted)
    {
        $this->is_accepted = $is_accepted;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $received_by;

    /**
     * @ORM\Column(type="integer")
     */
    private $sent_by;

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

    /**
     * @return mixed
     */
    public function getSentBy()
    {
        return $this->sent_by;
    }

    /**
     * @param mixed $sent_by
     */
    public function setSentBy($sent_by)
    {
        $this->sent_by = $sent_by;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



}