<?php 
namespace App\Models;

class Role extends CoreModel {
    
    private $roleString;
    

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