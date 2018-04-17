<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 19/1/2018
 * Time: 00:37
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChatPrivateRepository")
 * @ORM\Table(name="chat_private")
 * @ORM\AttributeOverrides({
 *     @ORM\AttributeOverride(name="chat_name",
 *          column=@ORM\Column(
 *          name="chat_name",
 *          length=191,
 *          unique=true
 *          )
 *      )
 * })
 */
class ChatPrivate
{
    /**
     * @return mixed
     */
    public function getInitiatedBy()
    {
        return $this->initiated_by;
    }

    /**
     * @param mixed $initiated_by
     */
    public function setInitiatedBy($initiated_by)
    {
        $this->initiated_by = $initiated_by;
    }

    /**
     * @return mixed
     */
    public function getChatWith()
    {
        return $this->chat_with;
    }

    /**
     * @param mixed $chat_with
     */
    public function setChatWith($chat_with)
    {
        $this->chat_with = $chat_with;
    }

    /**
     * @return mixed
     */
    public function getChatName()
    {
        return $this->chat_name;
    }

    /**
     * @param mixed $chat_name
     */
    public function setChatName($chat_name)
    {
        $this->chat_name = $chat_name;
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
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $chat_name;
    /**
     * @ORM\Column(type="integer")
     */
    private $initiated_by;
    /**
     * @ORM\Column(type="integer")
     */
    private $chat_with;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

}