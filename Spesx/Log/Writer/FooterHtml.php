<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FooterHtml
 *
 * @author hades
 */
class Spesx_Log_Writer_FooterHtml extends Zend_Log_Writer_Abstract {

    protected $LogConfig;

    public function __construct(Array $LogConfig) {
        $this->LogConfig = $LogConfig;
    }
    
    public static function factory($config) {
        
    }

    protected function _write($event) {
        return FALSE;
    }

    public function write($event) {
        if (
                isset($this->LogConfig['aff']['registryLabel']) &&
                !empty($this->LogConfig['aff']['registryLabel']) &&
                is_string($this->LogConfig['aff']['registryLabel']) &&
                Zend_Registry::isRegistered($this->LogConfig['aff']['registryLabel'])
        ) {
            $erroString = Zend_Registry::get($this->LogConfig['aff']['registryLabel']);
            $bool = true;
        } else {
            $erroString = Zend_Registry::get('HtmlLog');
            $bool = false;
        }

        if (empty($erroString)) {
            $erroString = '<h4>Log:</h4>' . PHP_EOL;
        }
        $erroString.="<li class='log'>";
        //11-Oct-2011 15:12:41 ALERT (1): test 127.0.0.1 );
        $erroString.= $event['ip'] . " : " . $event['timestamp'] . " " . $event['priorityName'] . " (" . $event['priority'] . "): " . $event['message'];
        $erroString.="</li>" . PHP_EOL;
        if ($bool) {
            Zend_Registry::set($this->LogConfig['aff']['registryLabel'], $erroString);
        } else {
            Zend_Registry::set('HtmlLog', $erroString);
        }
        return parent::write($event);
    }

}

?>
