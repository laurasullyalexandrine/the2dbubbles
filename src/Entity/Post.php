<?php 

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;

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
     * @var string|null
     */
    private ?string $created_at = null;

    /**
     * @var string|null
     */
    private ?string $updated_at = null;

    /**
     * @var int
     */
    private int $userId;

    /**
     * Get the value of id
     *
     * @return  integer
     */ 
    public function getId(): int
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
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of title
     *
     * @return  string
     */ 
    public function getTitle(): string
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
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of slug
     *
     * @return  string
     */ 
    public function getSlug(): string
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
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of chapo
     *
     * @return  string
     */ 
    public function getChapo(): string
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
    public function setChapo(string $chapo): self
    {
        $this->chapo = $chapo;

        return $this;
    }

    /**
     * Get the value of content
     *
     * @return  string
     */ 
    public function getContent(): string
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
    public function setContent(string $content): self
    {
        $this->content = $content;

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

    /**
     * Get the value of userId
     *
     * @return  int
     */ 
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param  int  $userId
     *
     * @return  self
     */ 
    public function setUserId(int $userId)
    {
        $this->userId = $userId;

        return $this;
    }
}
