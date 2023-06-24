<?php 

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;

class Comment 
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var int
     */
    private int $status;

    /**
     * @var string|null
     */
    private ?string $created_at = null;

    /**
     * @var string|null
     */
    private ?string $updated_at = null;
    
    /**
     * @var string
     */
    private ?string $roleString = null;

    /**
     * @var int
     */
    private ?int $userId = null;

    /**
     * @var int
     */
    private ?int $postId = null;


    /**
     * Get the value of id
     *
     * @return  int
     */ 
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  int  $id
     *
     * @return  self
     */ 
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the content of the entities Post and Comment
     *
     * @return  string
     */ 
    public function getContent(): string
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
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
    
    /**
     * Get the value of status
     *
     * @return  int
     */ 
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  int  $status
     *
     * @return  self
     */ 
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of created_at
     * Permet d'indiquer à twig que la valeur retourner doit être un objet de type DateTime()
     */ 
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at);
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
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->updated_at);
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
    public function getUserId(): int
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
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of posts
     *
     * @return  int
     */ 
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * Set the value of postId
     *
     * @param  int  $postId
     *
     * @return  self
     */ 
    public function setPostId(int $postId): self
    {
        $this->postId = $postId;

        return $this;
    }
}
