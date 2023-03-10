<?php 
namespace App\Models;

use DateTime;

class CoreModel {

    
    /**
     * The id of the entity
     *
     * @var int
     */
    protected $id;

    /**
     * The name of the entities Role and Tag
     *
     * @var string
     */
    protected $name;

    /**
     * The content of the entities Post and Comment
     *
     * @var string
     */
    protected $content;

    /**
     * The status of the entities Post and Comment
     *
     * @var bool
     */
    protected $status;
    
    protected $created_at;
    protected $updated_at;
    

    /**
     * Get the id of the entity
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the entity
     *
     * @param  int  $id  The id of the entity
     *
     * @return  self
     */ 
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the name of the entities Role and Tag
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the entities Role and Tag
     *
     * @param  string  $name  The name of the entities Role and Tag
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the content of the entities Post and Comment
     *
     * @return  string
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content of the entities Post and Comment
     *
     * @param  string  $content  The content of the entities Post and Comment
     *
     * @return  self
     */ 
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the status of the entities Post and Comment
     *
     * @return  bool
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the status of the entities Post and Comment
     *
     * @param  bool  $status  The status of the entities Post and Comment
     *
     * @return  self
     */ 
    public function setStatus(bool $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of created_at
     * Permet d'indiquer à twig que la valeur retourner doit être un objet de type DateTime()
     */ 
    public function getCreatedAt(): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at and creating a date format from the DateTime class
     */ 
    public function getUpdatedAt()
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->updated_at);
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */ 
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}