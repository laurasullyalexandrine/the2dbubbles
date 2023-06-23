<?php

declare(strict_types=1);

namespace App\Repository;

use DateTime;
use DateTimeInterface;

class CoreModel {
    
    /**
     * The id of the entity
     *
     * @var int
     */
    protected $id;
    
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
    
    /**
     * Get the id of the entity
     *
     * @return  int
     */ 
    public function getId(): int
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
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of created_at
     * Permet d'indiquer à twig que la valeur retourner doit être un objet de type DateTime()
     */ 
    public function getCreatedAt(): ?\DateTimeInterface
    {
        if ($this->created_at === null) {
            return null;
        } else {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at);
        }
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at and creating a date format from the DateTime class
     */ 
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        if ($this->updated_at === null) {
            return null;
        } else {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $this->updated_at);
        }
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */ 
    public function setUpdatedAt(DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
