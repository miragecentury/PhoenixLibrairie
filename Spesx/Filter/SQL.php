<?php

/**
 * Spesx_Filter_SQL : permet de filtrer les variable passés à la db afin d'eviter l'injection
 *
 * @author pewho
 */
class Spesx_Filter_SQL implements Zend_Filter_Interface
{
    /**
     * Permet d'echapper les valeurs de la string pouvant servir à l'injection sql
     * @param string $valeur
     * @return string
     */
    public function filter($valeur)
    {
        return $valeur;
    }

}

?>
