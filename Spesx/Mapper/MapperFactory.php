<?php

/**
 * Description of MapperFactory
 * Permet d'instancier un mapper particulier (Factory)
 *
 *
 * @author pewho
 */
class Spesx_Mapper_MapperFactory
{
    public static function getMapper($nomModele)
    {
        $nomModele = $nomModele . 'Mapper';
        return new $nomModele;
    }
}