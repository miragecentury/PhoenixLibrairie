<?php

/**
 * Exception de la Classe Spesx_Acl
 * 
 * @author VANROYE Victorien
 */
class Spesx_Acl_Exception extends Zend_Exception {

    protected $message = 'Spesx_Acl_Exception:';

    public function __construct($msg = '', $code = 0, Exception $previous = null) {
        $msg = $this->message . $msg;
        return parent::__construct($msg, $code, $previous);
    }

}

?>
