<?php

/** Déclaration de Spesx_Log
 */

/**
 *      Role : Cette classe entièrement statique assiste l'initialisation du
 *  système de Log d'une Application utilisant Zend_Framework via le passage en
 * paramètre d'un tableau de paramètre.
 * 
 * Le suport des tests unitaires a été fait mais les tests sont à écrire.
 *
 *  @author VANROYE Victorien
 *  @copyright Private
 *  @version 1.0
 */
class Spesx_Log {

    protected static $Zend_Log;
    protected static $Html_Log;
    // Isolation

    protected static $logConfig;

    // private static $triggerLog; enlever temporairement

    public static function ReturnZendLog() {
        if (is_a(self::$Zend_Log, 'Zend_Log')) {
            return self::$Zend_Log;
        } else {
            return self::ReturnEmptyLog();
        }
    }

    /**
     *  Role: Construit l'objet Zend_Log selon les paramètres de configuration
     *
     *  Exception :
     *      Spesx_Log_Exception peut être levé.
     *
     * @param array $logConfig Tableau contenant la configuration des Log
     * @param Zend_Log $log Object Zend_Log à paramétrer si pas spécifié
     * celui-ci sera créé
     * @return Zend_Log
     */
    public static function Factory(Array $logConfig, Zend_Log $log = null) {

        //$DefautConfig = Spesx_Log::DefautLogConfig;
        //test si les éléments primals ont été défini
        //test si un Zend_Log a été envoyé en paramètre
        if ($log === null) {
            $log = new Zend_Log();
        }

        $nombreWriter = 0;
        /* initialisation de la variable temporaire de controls du nombre de 
         * writer, Zend retrounant une erreur si aucun il est important de 
         * tester le nombre.
         */

        if (
                isset($logConfig['enable']) &&
                isset($logConfig['timeStampFormat']) &&
                isset($logConfig['ip']) &&
                isset($logConfig['ip']['enable']) &&
                isset($logConfig['db']) &&
                isset($logConfig['db']['enable']) &&
                isset($logConfig['aff']) &&
                isset($logConfig['aff']['enable']) &&
                isset($logConfig['stream']) &&
                isset($logConfig['stream']['enable'])
        ) {
            self::$logConfig = $logConfig;
            //sauvegarde de la configuration.
        } else {
            throw new Spesx_Log_Exception('Spesx_Log::Factory : Echec de chargement de la configuration');
            return self::ReturnEmptyLog();
        }

        //Si les logs sont activés Initialisation;
        if ($logConfig['enable'] == TRUE) {


            //test sur le timestamp si vide à voir si on peut test la validité
            if (empty($logConfig['timeStampFormat'])) {
                throw new Spesx_Log_Exception('Spesx_Log::Factory : Paramètre log.timeStampFormat Incorrecte ou Vide');
                return self::ReturnEmptyLog();
            } else {
                $log->setTimestampFormat($logConfig['timeStampFormat']);
            }

            //Définit l'item Ip si activé

            if ($logConfig['ip']['enable'] == TRUE) {
                if (self::TestIp($_SERVER['REMOTE_ADDR'])) {
                    $log->setEventItem('ip', $_SERVER['REMOTE_ADDR']);
                } else {
                    $logConfig['ip']['enable'] = 'off';
                }
            }

            if (
                    isset($logConfig['priority']) &&
                    preg_match("#[0-7]{1}#", $logConfig['priority'])
            ) {
                try {
                    $filter_int = new Zend_Filter_Int();
                    $filter = new Zend_Log_Filter_Priority($filter_int->filter($logConfig['priority']));
                    $log->addFilter($filter);
                } catch (Zend_Exception $e) {
                    throw new Spesx_Log_Exception('Spesx_Log::Factory : Impossible d\'initialiser le filtre principale', 0, $e);
                    return self::ReturnEmptyLog();
                } catch (Exception $e) {
                    throw new Spesx_Log_Exception('Spesx_Log::Factory : Impossible d\'initialiser le filtre principale', 0, $e);
                    return self::ReturnEmptyLog();
                }
            } else {
                throw new Spesx_Log_Exception('Spesx_Log::Factory : Paramètre log.priority Incorrecte ou Vide', 0, $e);
                return self::ReturnEmptyLog();
            }

            if (
                    isset($logConfig['aff']) &&
                    isset($logConfig['aff']['enable']) &&
                    $logConfig['aff']['enable'] == TRUE
            ) {
                $log = self::SetLogHtml($logConfig, $log);
                $nombreWriter++;
            }
            if (
                    isset($logConfig['stream']) &&
                    isset($logConfig['stream']['enable']) &&
                    $logConfig['stream']['enable'] == TRUE
            ) {
                $log = self::SetLogStream($logConfig, $log);
                $nombreWriter++;
            }
            if (
                    isset($logConfig['db']) &&
                    isset($logConfig['db']['enable']) &&
                    $logConfig['db']['enable'] == TRUE
            ) {
//$Log = self::SetLogDb($LogConfig, $Log);
//en attente de debug
                //$nombreWriter++;
            }

            //Permet de retourner un Zend_Log Valide pour Zend
            if ($nombreWriter == 0) {
                $log->addWriter(new Zend_Log_Writer_Null());
            }

            self::$Zend_Log = $log;
            return $log;
        } else {
//          Log désactivé
            return self::ReturnEmptyLog();
        }
    }

    /**
     *
     * @param array $logConfig
     * @param Zend_Log $log
     * @return Zend_Log
     */
    public static function SetLogStream(Array $logConfig, Zend_Log $log = null) {
        if ($log === null) {
            $log = new Zend_Log();
        }

        if (
                isset($logConfig['stream']) &&
                isset($logConfig['stream']['enable']) &&
                $logConfig['stream']['enable'] == TRUE
        ) {
            
        } else {
            
        }


        return $log;
    }

    /**
     *
     * @param array $LogConfig
     * @param Zend_Log $Log
     * @return Zend_Log
     */
    public static function SetLogDb(Array $logConfig, Zend_Log $log = null) {
        if ($log === null) {
            $log = new Zend_Log();
        }

        if (
                isset($logConfig['db']) &&
                isset($logConfig['db']['enable']) &&
                $logConfig['db']['enable'] == TRUE
        ) {
            
        }


        return $log;
    }

    /**
     *  Role:   Paramétrage de Zend_Log pour un environnement de développement.
     *
     *      - Writer dans un fichier
     *      - Writer dans le footer
     *
     *
     *  Exception:
     *      Spesx_Log_Exception peut être levé
     *
     * @param array $logConfig
     * @param Zend_Log $log
     * @return Zend_Log
     */
    public static function SetLogHtml(Array $logConfig, Zend_Log $log = null) {
        if ($log === null) {
            $log = new Zend_Log();
        }

        if (
                isset($logConfig['aff']) &&
                isset($logConfig['aff']['enable']) &&
                $logConfig['aff']['enable'] == TRUE
        ) {

//Initialisation de la Valeur dans le registre
            $htmlLog = '<h4>Log:</h4><ul><li>Bootstrap : Erreur à l\'initialisation de Insset_Log_Writer_FooterHtml : Fermeture Application</li></ul>' . PHP_EOL;

            try {
                $writer_FooterHtml = new Spesx_Log_Writer_FooterHtml($logConfig);
                $htmlLog = "";
            } catch (Exception $e) {
                $htmlLog = "Bootstrap : Exception : Erreur à l\'initialisation de Insset_Log_Writer_FooterHtml : Fermeture Application";
            }

            try {
                $log->addWriter($writer_FooterHtml);
            } catch (Zend_Exception $e) {
                $htmlLog = 'Erreur';
            } catch (Exception $e) {
                $htmlLog = 'Erreur';
            }

            if (
                    isset($logConfig['aff']['registryLabel']) &&
                    !empty($logConfig['aff']['registryLabel']) &&
                    is_string($logConfig['aff']['registryLabel']) &&
                    !Zend_Registry::isRegistered($logConfig['aff']['registryLabel'])
            ) {
                Zend_Registry::set($logConfig['aff']['registryLabel'], $htmlLog);
            } else {
                Zend_Registry::set('HtmlLog', $htmlLog);
            }

            return $log;
        } else {
            return self::ReturnEmptyLog();
        }
    }

    /**
     *
     */
    public static function GetLogHtml() {
        if (
                isset(self::$logConfig['aff']) &&
                isset(self::$logConfig['aff']['enable']) &&
                self::$logConfig['aff']['enable'] == TRUE
        ) {
            $post = '<ul>';
            $end = PHP_EOL . '</ul>';
            if (
                    isset(self::$logConfig['aff']['registryLabel']) &&
                    !empty(self::$logConfig['aff']['registryLabel']) &&
                    is_string(self::$logConfig['aff']['registryLabel']) &&
                    Zend_Registry::isRegistered(self::$logConfig['aff']['registryLabel'])
            ) {
                return $post . Zend_Registry::get(self::$logConfig['aff']['registryLabel']) . $end;
            } else {
                if (Zend_Registry::isRegistered('HtmlLog')) {
                    return $post . Zend_Registry::get('HtmlLog') . $end;
                } else {
                    return '';
                }
            }
        } else {
            return '';
        }
    }

    /**
     *
     * @param String $msg       Message dans les logs
     * @param int $priority     Valeur numérique étant 
     * @return mixed
     */
    public static function Log($msg, $priority) {
        if (
                $priority === Zend_Log::EMERG ||
                $priority === Zend_Log::ALERT ||
                $priority === Zend_Log::CRIT ||
                $priority === Zend_Log::ERR ||
                $priority === Zend_Log::WARN ||
                $priority === Zend_Log::NOTICE ||
                $priority === Zend_Log::INFO ||
                $priority === Zend_Log::DEBUG
        ) {
            //ok
        } else {
            $priority == Zend_Log::DEBUG;
        }

        if (is_string($msg)) {
            //ok
        } else {
            $priority = Zend_Log::ERR;
            $msg = "Erreur de valeur de \$priority dans l'appel de Log ou Log[Level]";
        }
        return self::$Zend_Log->log($msg, $priority);
        //return Zend_Registry::get('Log')->log($msg, $priority);
    }

    /**
     *
     * @param type $msg
     * @return type 
     */
    public static function LogALERT($msg) {
        return self::$Zend_Log->log($msg, Zend_Log::ALERT);
    }

    /**
     *
     * @param type $msg
     * @return type 
     */
    public static function LogERR($msg) {
        return self::$Zend_Log->log($msg, Zend_Log::ERR);
    }

    /**
     *
     * @param type $msg
     * @return type 
     */
    public static function LogINFO($msg) {
        return self::$Zend_Log->log($msg, Zend_Log::INFO);
    }

    /**
     *
     * @param type $msg
     * @return type 
     */
    public static function LogCRIT($msg) {
        return self::$Zend_Log->log($msg, Zend_Log::CRIT);
    }

    /**
     *
     * @param type $msg
     * @return type 
     */
    public static function LogEMERG($msg) {
        return self::$Zend_Log->log($msg, Zend_Log::EMERG);
    }

    /**
     *
     * @param type $msg
     * @return type 
     */
    public static function LogNOTICE($msg) {
        return self::$Zend_Log->log($msg, Zend_Log::NOTICE);
    }

    /**
     *
     * @param type $msg
     * @return type 
     */
    public static function LogWARN($msg) {
        return self::$Zend_Log->log($msg, Zend_Log::WARN);
    }

    /**
     * Fonction retournant un Zend_Log Null Valide (utile dans les tests ou 
     * en cas d'erreur(s).
     * @return Zend_Log 
     */
    public static function ReturnEmptyLog() {
        $log = new Zend_Log();
        $log->addWriter(new Zend_Log_Writer_Null());
        self::$Zend_Log = $log;
        return $log;
    }

    /* ====================================================================== */

    /**
     * Fonction de Test des formats d'Ip
     * @param mixed $ip
     * @return Boolean
     */
    private static function TestIp($ip) {
        try {
            $validate_ip = new Zend_Validate_Ip();
            $resultat = $validate_ip->isValid($ip);
            unset($validate_ip);
        } catch (Zend_Exception $e) {
            throw new Spesx_Log_Exception('Ip invalide', 0, $e);
            return self::ReturnEmptyLog(self::$logConfig);
        } catch (Exception $e) {
            throw new Spesx_Log_Exception('Ip invalide', 0, $e);
            return self::ReturnEmptyLog(self::$logConfig);
        }
        return $resultat;
    }

}