<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cache
 *
 * @author grp3
 */
class Spesx_Cache {

    private static $Zend_Log;
    private static $Zend_Cache;

    /**
     *
     * @param array $config
     * @param Zend_Log $Zend_Log
     * @return type 
     */
    public static function factory(Array $config, Zend_Log $Zend_Log = null) {

        if ($Zend_Log === null) {
            $Zend_Log = Spesx_Log::ReturnEmptyLog();
        }

        //var_dump($config);
        if (is_array($config)) {
            if (
                    isset($config['enable']) && !empty($config['enable']) &&
                    isset($config['frontend']) && is_array($config['frontend']) &&
                    isset($config['frontend']['debugenable']) && !empty($config['frontend']['debugenable']) &&
                    isset($config['frontend']['cache_id_prefix']) && !empty($config['frontend']['cache_id_prefix']) &&
                    isset($config['frontend']['lifetime']) && !empty($config['frontend']['lifetime']) &&
                    isset($config['frontend']['auto_serialize']) && !empty($config['frontend']['auto_serialize']) &&
                    isset($config['frontend']['auto_cleaning']) && !empty($config['frontend']['auto_cleaning']) &&
                    isset($config['backend']) && is_array($config['backend']) &&
                    isset($config['backend']['type']) && !empty($config['backend']['type']) &&
                    (
                    $config['backend']['type'] == 'Libmemcached' && isset($config['backend']['host']) && isset($config['backend']['port'])
                    )
            ) {
                $frontend_config = array();

                if ($config['enable'] == TRUE) {

                    if ($config['frontend']['debugenable'] == TRUE) {
                        $frontend_config['caching'] = TRUE;
                    } else {
                        $frontend_config['caching'] = FALSE;
                    }

                    $frontend_config['cache_id_prefix'] = $config['frontend']['cache_id_prefix'];

                    $frontend_config['lifetime'] = $config['frontend']['lifetime'];

                    $frontend_config['logging'] = FALSE;

                    $frontend_config['write_control'] = TRUE;

                    if ($config['frontend']['auto_serialize'] == TRUE) {
                        $frontend_config['automatic_serialization'] = TRUE;
                    } else {
                        $frontend_config['automatic_serialization'] = FALSE;
                    }

                    if (is_int($config['frontend']['auto_cleaning'])) {
                        $frontend_config['automatic_cleaning_factor'] = $config['frontend']['auto_cleaning'];
                    } else {
                        $frontend_config['automatic_cleaning_factor'] = 1;
                    }


                    $backend_config = array();

                    if ($config['backend']['type'] == 'Libmemcached') {
                        if (self::TestIp($config['backend']['host'])) {
                            $backend_config['host'] = $config['backend']['host'];
                        } else {
                            $Zend_Log->log('backend host incorréhent le valeur par défaut a été chargé', Zend_Log::ERR);
                            $backend_config['host'] = '127.0.0.1';
                        }

                        if (0 <= $config['backend']['port'] && $config['backend']['port'] < 655356) {
                            $backend_config['port'] = $config['backend']['port'];
                        } else {
                            $Zend_Log->log('Bakcend port incorréhent le valeur par défaut a été chargé', Zend_Log::ERR);
                            $backend_config['port'] = 11211;
                        }

                        $backend = new Zend_Cache_Backend_Libmemcached($backend_config);
                    }
                    $frontend = new Zend_Cache_Core($frontend_config);

                    $cache = Zend_Cache::factory($frontend, $backend);

                    if ($cache != FALSE && is_a($cache, 'Zend_Cache_Core')) {
                        self::$Zend_Cache = $cache;
                    } else {
                        $cache = self::ReturnBlackHoleCache();
                    }

                    //cleaning
                    unset($frontend_config);
                    unset($backend_config);

                    //echo 'Retourne valide cache <br/>';
                    return $cache;
                } else {
                    $Zend_Log->log('Cache Désactivé', Zend_Log::INFO);
                    return self::ReturnBlackHoleCache();
                }
            } else {
                throw new Spesx_Cache_Exception('Paramètre de Configuration Incorrecte');
                $Zend_Log->log('Paramètre de Configuration Incorrecte', Zend_Log::CRIT);
            }

            return self::ReturnBlackHoleCache();
        } else {

            return self::ReturnBlackHoleCache();
        }
    }

    /**
     * Retourne un Zend_Cache de type Trou Noire donc fictif
     * @return Zend_Cache
     */
    public static function ReturnBlackHoleCache() {
        $cache = Zend_Cache::factory(new Zend_Cache_Core(), new Zend_Cache_Backend_BlackHole());
        self::$Zend_Cache = $cache;
        return $cache;
    }

    /**
     * Retourne le Zend_Cache précédemment générait ou
     * @return Zend_Cache
     */
    public static function ReturnZendCache() {
        if (isset(self::$Zend_Cache) && is_a(self::$Zend_Cache, 'Zend_Cache')) {
            return self::$Zend_Cache;
        } else {
            return self::ReturnBlackHoleCache();
        }
    }

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

?>
