<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Spesx_Acl {

    private static $Zend_Log;
    private static $Zend_Cache;
    private static $Zend_Acl;
    private static $msg = 'Spesx_Acl: ';

//Isolation

    public static function factory(Array $config, Zend_Log $Zend_Log = null, Zend_Cache_Core $Zend_Cache =null, Zend_Acl $Zend_Acl = null) {
        if ($Zend_Log == null) {
            $Zend_Log = Spesx_Log::ReturnEmptyLog();
        }

        self::$Zend_Log = $Zend_Log;

        if ($Zend_Cache == null) {
            $Zend_Cache = Spesx_Cache::ReturnBlackHoleCache();
        }

        self::$Zend_Cache = $Zend_Cache;

        //var_dump($config);
        //echo '<br/>';

        if (
                isset($config['enable']) &&
                isset($config['cache']['enable'])
        ) {
            if ($config['enable'] == TRUE) {
                self::$Zend_Log->log('Acl Activé', Zend_Log::INFO);
                if ($config['cache']['enable'] == TRUE) {
                    self::$Zend_Log->log('Acl-Cache Activé', Zend_Log::INFO);
                    return self::GetAclByCache($config);
                } else {
                    self::$Zend_Log->log('Acl-Cache Désactivé', Zend_Log::INFO);
                    return self::InitialisationAcl($config);
                }
            } else {
                self::$Zend_Log->log('Acl Désactivé', Zend_Log::INFO);
                return self::ReturnEmptyAcl();
            }
        } else {
            self::$Zend_Log->log('Paramètre de Configuration Incorrecte', Zend_Log::CRIT);
            throw new Spesx_Acl_Exception('Paramètre de Configuration Incorrecte');
            return self::ReturnEmptyAcl();
        }
    }

    private static function GetAclByCache(Array $config) {
        var_dump($config);
        if (
                isset($config['cache']['id']) && !empty($config['cache']['id']) &&
                isset($config['cache']['lifetime'])
        ) {
            if (self::$Zend_Cache->test($config['cache']['id'])) {
                self::$Zend_Log->log('Acl Chargé dans le Cache', Zend_Log::INFO);
                return unserialize(self::$Zend_Cache->load($config['cache']['id']));
            } else {
                self::$Zend_Log->log('Acl Généré et save dans le Cache', Zend_Log::INFO);
                $acl = self::InitialisationAcl($config);
                self::$Zend_Cache->save(serialize($acl), $config['cache']['id']);
                return $acl;
            }
        } else {
            throw new Spesx_Acl_Exception('Paramètre de Configuration du Cache Acl Incorrecte');
            return self::ReturnEmptyAcl();
        }
    }

    private static function log($msg, $priority) {
        
    }

    private static function InitialisationAcl(Array $config) {

        if (
                isset($config['active_assertion']) &&
                isset($config['save']) && is_array($config['save']) &&
                isset($config['save']['type']) && !empty($config['save']['type']) &&
                (
                ($config['save']['type'] == 'ini' && isset($config['save']['path']) && !empty($config['save']['path']))
                )
        ) {

            if ($config['save']['type'] == 'ini') {

                //stockage in ini
                if (is_file($config['save']['path'])) {
                    return self::IniToAcl($config['save']['path']);
                } else {
                    throw new Spesx_Acl_Exception('Paramètre de Configuration Incorrecte');
                    return self::ReturnEmptyAcl();
                }

                //other stockage
                //end
            }
        } else {
            throw new Spesx_Acl_Exception('Paramètre de Configuration Incorrecte');
            return self::ReturnEmptyAcl();
        }
    }

    public static function ReturnEmptyAcl() {
        self::$Zend_Acl = new Zend_Acl();
        return self::$Zend_Acl;
    }

    private static function IniToAcl($PathToIniAclFile = '../application/configs/acl.ini') {
        $acl = new Zend_Acl();
        $validate_file_exists = new Zend_Validate_File_Exists();
        $validate_file_extension = new Zend_Validate_File_Extension('ini');

//if ($validate_file_exists->isValid($PathToIniAclFile) && $validate_file_extension->isValid($PathToIniAclFile)) {
        if (is_file($PathToIniAclFile)) {
            try {
                $acl_role = new Zend_Config_Ini($PathToIniAclFile, 'roles');
                $acl_resources = new Zend_Config_Ini($PathToIniAclFile, 'resources');
                $acl_role = $acl_role->toArray();
                $acl_resources = $acl_resources->toArray();
            } catch (Zend_Config_Exception $e) {
                Zend_Registry::get('Log')->log('Bootstrap : getAcl : Forme de ' . $PathToIniAclFile . ' Incorrecte', Zend_Log::ALERT);
                return FALSE;
            } catch (Exception $e) {
                Zend_Registry::get('Log')->log('Bootstrap : getAcl : Forme de ' . $PathToIniAclFile . ' Incorrecte', Zend_Log::ALERT);
                return FALSE;
            }

            foreach ($acl_role as $key => $value) {
                $acl->addRole($key);
                
            }

            foreach ($acl_resources as $key => $value) {
                $acl->addResource($key);
                if (isset($value['allow']) && (count($value['allow']) > 0)) {
                    foreach ($value['allow'] as $roles) {
                        $acl->allow($roles, $key);
                    }
                }
                if (isset($value['deny']) && (count($value['deny']) > 0)) {
                    foreach ($value['deny'] as $roles) {
                        $acl->deny($roles, $key);
                    }
                }            
            }      
            return $acl;
        } else {
            self::$Zend_Log->log('Bootstrap : getAcl : Impossible de charger le fichier .ini des acl Vérifier le fichier de configuration', Zend_Log::ALERT);
            return FALSE;
        }



//nettoyage
        unset($validate_file_exists);
        unset($validate_file_extension);
    }

}

?>
