<?php

/**
 * Exception de la Classe Application_Model_Mapper
 *
 * @author Pewho
 */
class Spesx_Mapper_Exception extends Zend_Exception {
    protected $message = 'Spesx_Mapper_Exception :';

    public function __construct($msg = '', $code = 0, Exception $previous = null) {
        $msg = $this->message . $msg;
        return parent::__construct($msg, $code, $previous);
    }

}
