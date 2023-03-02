<?php 
namespace App\Models;

class User {
    
    private $email;
    private $password;
    private $id_post;
    

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of id_post
     */ 
    public function getId_post()
    {
        return $this->id_post;
    }

    /**
     * Set the value of id_post
     *
     * @return  self
     */ 
    public function setId_post($id_post)
    {
        $this->id_post = $id_post;

        return $this;
    }
}