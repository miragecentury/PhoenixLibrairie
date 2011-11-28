<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_Storage_MySession
 *
 * @author independance
 */
class Zend_Storage_MySession extends Zend_Auth_Storage_Session {



    function setRole($role) {
        $this->_session->role = $role;
    }

    function getRole() {
        return $this->_session->role->role;
    }

}

?>
