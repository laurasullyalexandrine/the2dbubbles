<?php 

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;

class Role
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private ?string $roleString = null;

    /**
     * @var string|null
     */
    private ?string $created_at = null;

    /**
     * @var string|null
     */
    private ?string $updated_at = null;
    



    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Get the value of id
     */ 
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id):self
    {
        $this->id = $id;

        return $this;
    }
/**
     * Get the value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of roleString
     */
    public function getRoleString(): ?string
    {
        return $this->roleString;
    }

    /**
     * Set the value of roleString
     *
     * @return  self
     */
    public function setRoleString(string $roleString): self
    {
        $this->roleString = $roleString;

        return $this;
    }

    /**
     * Méthode permettant d'afficher les roles
     *
     * @return string
     */
    public function getDisplayRole(): string
    {
        if ($this->roleString === "ROLE_SUPER_ADMIN") {
            return "Super admin";
        } elseif ($this->roleString === "ROLE_SUPER_ADMIN") {
            return "admin";
        } else {
            return "utilisateur";
        }
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
    public function getUpdatedAt(): ?DateTimeInterface
    {
        if ($this->created_at === null) {
            return null;
        } else {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at);
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
