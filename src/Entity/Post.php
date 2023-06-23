<?php 

namespace App\Entity;

class Post
{
    /**
     * @var integer
     */
    private int $id;

    /**
     * @var string
     */
    private ?string $title = null;

    /**
     * @var string
     */
    private ?string $slug = null;

    /**
     * @var string
     */
    private ?string $chapo = null;

    /**
     * @var string
     */
    private ?string $content = null;

    /**
     * @var int
     */
    private int $users;

    /**
     * Get the value of id
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  integer  $id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    
    /**
     * Get the value of users
     *
     * @return  int
     */ 
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set the value of users
     *
     * @param  int  $users
     *
     * @return  self
     */ 
    public function setUsers(int $users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get the value of title
     *
     * @return  string
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
     *
     * @return  self
     */ 
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of slug
     *
     * @return  string
     */ 
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @param  string  $slug
     *
     * @return  self
     */ 
    public function setSlug(string $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of chapo
     *
     * @return  string
     */ 
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * Set the value of chapo
     *
     * @param  string  $chapo
     *
     * @return  self
     */ 
    public function setChapo(string $chapo)
    {
        $this->chapo = $chapo;

        return $this;
    }

    /**
     * Get the value of content
     *
     * @return  string
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  string  $content
     *
     * @return  self
     */ 
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }
}
