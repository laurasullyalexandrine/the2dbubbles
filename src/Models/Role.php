<?php 
namespace App\Models;

class Role extends CoreModel {
    
    private $roleString;
    private $id_user;
    

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

    /**
     * Get the value of id_user
     */ 
    public function getId_user()
    {
        return $this->id_user;
    }

    /**
     * Set the value of id_user
     *
     * @return  self
     */ 
    public function setId_user($id_user)
    {
        $this->id_user = $id_user;

        return $this;
    }
}