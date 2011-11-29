<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Exception de la Classe Spesx_Cache
 * 
 * @author VANROYE Victorien
 */
class Spesx_Cache_Exception extends Zend_Exception {

    protected $message = 'Spesx_Cache_Exception:';

    public function __construct($msg = '', $code = 0, Exception $previous = null) {
        $msg = $this->message . $msg;
        return parent::__construct($msg, $code, $previous);
    }

}

?>
