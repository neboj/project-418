<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 19/1/2018
 * Time: 00:44
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChatMessageRepository")
 * @ORM\Table(name="chat_message")
 * @ORM\AttributeOverrides({
 *     @ORM\AttributeOverride(name="chat_name",
 *          column=@ORM\Column(
 *          name="chat_name",
 *          length=191,
 *          unique=false
 *          )
 *      )
 * })
 */
class ChatMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $chat_name;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $message_id;
    /**
     * @ORM\Column(type="integer")
     */
    private $sent_by;

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
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * @param mixed $message_id
     */
    public function setMessageId($message_id)
    {
        $this->message_id = $message_id;
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
     * @ORM\Column(type="integer")
     */
    private $received_by;
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string")
     */
    private $message_content;

    /**
     * @return mixed
     */
    public function getMessageContent()
    {
        return $this->message_content;
    }

    /**
     * @param mixed $message_content
     */
    public function setMessageContent($message_content)
    {
        $this->message_content = $message_content;
    }


    /**
     * @ORM\Column(type="boolean")
     */
    private $is_read;

    /**
     * @return mixed
     */
    public function getisRead()
    {
        return $this->is_read;
    }

    /**
     * @param mixed $is_read
     */
    public function setIsRead($is_read)
    {
        $this->is_read = $is_read;
    }

}