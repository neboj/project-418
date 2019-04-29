<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 19/10/2017
 * Time: 21:11
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="images")
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getFullPath()
    {
        return $this->full_path;
    }

    /**
     * @param mixed $full_path
     */
    public function setFullPath($full_path)
    {
        $this->full_path = $full_path;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getUploadedBy()
    {
        return $this->uploaded_by;
    }

    /**
     * @param mixed $uploaded_by
     */
    public function setUploadedBy($uploaded_by)
    {
        $this->uploaded_by = $uploaded_by;
    }

    /**
     * @return mixed
     */
    public function getisProfile()
    {
        return $this->is_profile;
    }

    /**
     * @param mixed $is_profile
     */
    public function setIsProfile($is_profile)
    {
        $this->is_profile = $is_profile;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $full_path;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\Column(type="integer")
     */
    private $uploaded_by;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_profile;



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}