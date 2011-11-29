<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Spesx_Acl {

    private static $Zend_Log;
    private static $Zend_Cache;
    private static $Zend_Acl;

    //Isolation

    public static function factory(Array $configuration, Zend_Acl $Zend_Acl = null, Zend_Log $Zend_Log = null, Zend_Cache $Zend_Cache =null) {
        if ($Zend_Log == null) {
            $Zend_Log = Spesx_Log::ReturnEmptyLog();
        }

        if ($Zend_Log == null) {
            $Zend_Cache = Spesx_Cache::ReturnBlackHoleCache();
        }
        
        
    }

    public static function Old_Init() {
        $filter_bool = new Zend_Filter_Boolean('all');
        $validate_file_exists = new Zend_Validate_File_Exists();
        $validate_file_extension = new Zend_Validate_File_Extension('ini');

        try {
            $config = $this->getOption('acl');
            $config_cache = $this->getOption('cache');
        } catch (Exception $e) {
            Zend_Registry::get('Log');
            return FALSE;
        }

        if (isset($config['active']) && !empty($config['active'])) {
            $config['active'] = $filter_bool->filter($config['active']);
        } else {
            $config['active'] = FALSE;
        }

        if (isset($config['active_assertion']) && !empty($config['active_assertion'])) {
            $config['active_assertion'] = $filter_bool->filter($config['active_assertion']);
        } else {
            $config['active_assertion'] = FALSE;
        }
        //echo '../application/configs/' . $config['filename'];
        //var_dump($validate_file_exists->isValid('../application/configs/' . $config['filename']));
        //if ($config['active'] && $validate_file_exists->isValid('../application/configs/'.$config['filename']) && $validate_file_extension->isValid('../application/configs/'.$config['filename'])) {
        if ($config['active']) {
            if (isset($config['cache']['active']) && !empty($config['cache']['active'])) {
                $config['cache']['active'] = $filter_bool->filter($config['cache']['active']);
            }
            if ((Zend_Registry::get('Cache') != FALSE)) {
                if ($config['cache']['active']) {
                    $cache = Zend_Registry::get('Cache');
                    if (!($data = $cache->load($config_cache['idApplication'] . 'Acl')) && FALSE) {
                        echo 'Acl get in cache';
                        //var_dump($data);
                        //$data = Zend_Serializer::unserialize($data);
                        return $data;
                    } else {
                        echo 'Acl set in cache';
                        $acl = $acl = $this->getAcl('../application/configs/' . $config['filename']);
                        $Sadap = new Zend_Serializer_Adapter_Amf0();
                        Zend_Serializer::setDefaultAdapter($Sadap);
                        $data = Zend_Serializer::serialize($acl);

                        var_dump($data);
                        try {
                            $cache->save($config_cache['idApplication'] . 'Acl', $data);
                        } catch (Exception $e) {
                            Zend_Registry::get('Log')->log('Bootstrap : _initAcl : Exception : Impossible de Mettre l\'Acl dans le Cache', Zend_Log::ALERT);
                        }
                    }
                    return $acl;
                } else {
                    Zend_Registry::set('Acl', ($acl = $this->getAcl('../application/configs/' . $config['filename'])));
                    return $acl;
                }
            } else {
                Zend_Registry::set('Acl', ($acl = $this->getAcl('../application/configs/' . $config['filename'])));
                return $acl;
            }
        } else {
            Zend_Registry::set('Acl', FALSE);
            Zend_Registry::get('Log')->log('Bootstrap : _initAcl : Acl Désactivé', Zend_Log::DEBUG);
            return FALSE;
        }

//nettoyage
        unset($filter_bool);
        unset($validate_file_exists);
        unset($validate_file_extension);
        $filter_bool = new Zend_Filter_Boolean('all');
        $validate_file_exists = new Zend_Validate_File_Exists();
        $validate_file_extension = new Zend_Validate_File_Extension('ini');

        try {
            $config = $this->getOption('acl');
            $config_cache = $this->getOption('cache');
        } catch (Exception $e) {
            Zend_Registry::get('Log');
            return FALSE;
        }

        if (isset($config['active']) && !empty($config['active'])) {
            $config['active'] = $filter_bool->filter($config['active']);
        } else {
            $config['active'] = FALSE;
        }

        if (isset($config['active_assertion']) && !empty($config['active_assertion'])) {
            $config['active_assertion'] = $filter_bool->filter($config['active_assertion']);
        } else {
            $config['active_assertion'] = FALSE;
        }
        //echo '../application/configs/' . $config['filename'];
        //var_dump($validate_file_exists->isValid('../application/configs/' . $config['filename']));
        //if ($config['active'] && $validate_file_exists->isValid('../application/configs/'.$config['filename']) && $validate_file_extension->isValid('../application/configs/'.$config['filename'])) {
        if ($config['active']) {
            if (isset($config['cache']['active']) && !empty($config['cache']['active'])) {
                $config['cache']['active'] = $filter_bool->filter($config['cache']['active']);
            }
            if ((Zend_Registry::get('Cache') != FALSE)) {
                if ($config['cache']['active']) {
                    $cache = Zend_Registry::get('Cache');
                    if (!($data = $cache->load($config_cache['idApplication'] . 'Acl')) && FALSE) {
                        echo 'Acl get in cache';
                        //var_dump($data);
                        //$data = Zend_Serializer::unserialize($data);
                        return $data;
                    } else {
                        echo 'Acl set in cache';
                        $acl = $acl = $this->getAcl('../application/configs/' . $config['filename']);
                        $Sadap = new Zend_Serializer_Adapter_Amf0();
                        Zend_Serializer::setDefaultAdapter($Sadap);
                        $data = Zend_Serializer::serialize($acl);

                        var_dump($data);
                        try {
                            $cache->save($config_cache['idApplication'] . 'Acl', $data);
                        } catch (Exception $e) {
                            Zend_Registry::get('Log')->log('Bootstrap : _initAcl : Exception : Impossible de Mettre l\'Acl dans le Cache', Zend_Log::ALERT);
                        }
                    }
                    return $acl;
                } else {
                    Zend_Registry::set('Acl', ($acl = $this->getAcl('../application/configs/' . $config['filename'])));
                    return $acl;
                }
            } else {
                Zend_Registry::set('Acl', ($acl = $this->getAcl('../application/configs/' . $config['filename'])));
                return $acl;
            }
        } else {
            Zend_Registry::set('Acl', FALSE);
            Zend_Registry::get('Log')->log('Bootstrap : _initAcl : Acl Désactivé', Zend_Log::DEBUG);
            return FALSE;
        }

//nettoyage
        unset($filter_bool);
        unset($validate_file_exists);
        unset($validate_file_extension);
    }

    private function getAcl($PathToIniAclFile = '../application/configs/acl.ini') {
        $acl = new Zend_Acl();
        $validate_file_exists = new Zend_Validate_File_Exists();
        $validate_file_extension = new Zend_Validate_File_Extension('ini');

        //if ($validate_file_exists->isValid($PathToIniAclFile) && $validate_file_extension->isValid($PathToIniAclFile)) {
        if (TRUE) {
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

            $trigger_role_herit = array();
            foreach ($acl_role as $key => $value) {
                $acl->addRole($key);
                if (isset($value['herit']) && (count($value['herit']) > 0)) {
                    $trigger_role_herit[] = $key;
                }
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
            Zend_Registry::get('Log')->log('Bootstrap : getAcl : Impossible de charger le fichier .ini des acl Vérifier le fichier de configuration', Zend_Log::ALERT);
            return FALSE;
        }



        //nettoyage
        unset($validate_file_exists);
        unset($validate_file_extension);
    }

}

?>
