<?php 

namespace App\Entity;

class Role
{
    private $id;

    private $name;

    private $roleString;

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of roleString
     */ 
    public function getRoleString()
    {
        return $this->roleString;
    }

    /**
     * Set the value of roleString
     *
     * @return  self
     */ 
    public function setRoleString($roleString)
    {
        $this->roleString = $roleString;

        return $this;
    }
}