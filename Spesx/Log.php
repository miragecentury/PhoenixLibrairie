<?php

/** Déclaration de Spesx_Log
 */

/**
 *      Role : Cette classe entièrement statique assiste l'initialisation du
 *  système de Log d'une Application utilisant Zend_Framework via le passage en
 * paramètre d'un tableau de paramètre.
 *
 *  @author VANROYE Victorien
 *  @copyright Private
 *  @version 1.0
 */
class Spesx_Log {

    protected static $logConfig;
    private static $triggerLog;

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

        if (
                isset($logConfig['enable']) &&
                isset($logConfig['registryLabel']) &&
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
        } else {
            throw new Spesx_Log_Exception('Spesx_Log::Factory : Echec de chargement de la configuration');
            return self::ReturnEmptyLog($logConfig);
        }

//Si les logs sont activés Initialisation;
        if ($logConfig['enable'] == TRUE) {


//test sur le timestamp si vide à voir si on peut test la validité
            if (empty($logConfig['timeStampFormat'])) {
                throw new Spesx_Log_Exception('Spesx_Log::Factory : Paramètre log.timeStampFormat Incorrecte ou Vide');
                return self::ReturnEmptyLog($logConfig);
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
                    return self::ReturnEmptyLog($logConfig);
                } catch (Exception $e) {
                    throw new Spesx_Log_Exception('Spesx_Log::Factory : Impossible d\'initialiser le filtre principale', 0, $e);
                    return self::ReturnEmptyLog($logConfig);
                }
            } else {
                throw new Spesx_Log_Exception('Spesx_Log::Factory : Paramètre log.priority Incorrecte ou Vide', 0, $e);
                return self::ReturnEmptyLog($logConfig);
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

            if ($nombreWriter == 0) {
                $log->addWriter(new Zend_Log_Writer_Null());
            }

            if (
                    isset($logConfig['registryLabel']) &&
                    !empty($logConfig['registryLabel']) &&
                    is_string($logConfig['registryLabel']) &&
                    !Zend_Registry::isRegistered($logConfig['registryLabel'])
            ) {
                Zend_Registry::set($logConfig['registryLabel'], $log);
            } else {
                $log = self::ReturnEmptyLog($logConfig);
                Zend_Registry::set('Log', $log);
                throw new Spesx_Log_Exception('Erreur Set Registry');
            }
            return $log;
        } else {
//          Log désactivé
            return self::ReturnEmptyLog($logConfig);
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
            return self::ReturnEmptyLog($logConfig);
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
     * @param array $LogConfig
     * @param Zend_Log $Log
     * @return Zend_Log
     */
    public static function ReloadLog(Array $logConfig, Zend_Log $log = null) {
//? ip
        if ($log === null) {
            $log = new Zend_Log();
        }



        return $log;
    }

    /**
     *
     * @param String $msg
     * @param int $priority
     * @return mixed
     */
    public static function Log($msg, $priority) {
        return Zend_Registry::get('Log')->log($msg, $priority);
    }

    /* ====================================================================== */

    /**
     *
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

    private static function ReturnEmptyLog(Array $logConfig) {
        $log = new Zend_Log();
        $log->addWriter(new Zend_Log_Writer_Null());
        if (
                isset($logConfig['registryLabel']) &&
                !empty($logConfig['registryLabel']) &&
                is_string($logConfig['registryLabel']) &&
                !Zend_Registry::isRegistered($logConfig['registryLabel'])
        ) {
            Zend_Registry::set($logConfig['registryLabel'], $log);
        } else {
            Zend_Registry::set('Log', $log);
        }
        return $log;
    }

    private static function TriggerLog(String $msg, Int $priority) {

    }

}