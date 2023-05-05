<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

class CoreModel {
    
    /**
     * The id of the entity
     *
     * @var int
     */
    protected $id;
    
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
        if ($this->updated_at == null) {
            return;
        } else {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $this->updated_at);
        }
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