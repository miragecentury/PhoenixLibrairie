<?php

abstract class Spesx_Mapper_Mapper
{

    protected $_dbTable;

    //Permet de récupérer un objet avec les données correspondantes
    abstract protected function _createItemFromRow(Zend_Db_Table_Row $row);

    //Permet de créer la liste des objets lors d'un fetch_all()
    protected function _createItemsFromRowset(Zend_Db_Table_Rowset $rowset)
    {
        $items = array();
        foreach ($rowset as $row) {
            $items[] = $this->_createItemFromRow($row);
        }
        return $items;
    }

    //Permet de récupérer les informations d'un objet
    abstract protected function _getDataArrayFromItem($item);

    //Permet de récupérer la source de données
    protected function _getDbTableName()
    {
        //Récupère le nom complet de la classe (Application_Module_...)
        $class = get_class($this);
        /* Extrait chaque partie du nom de la classe dans un tableau
          en enlevant les _ */
        $parts = explode('_', $class);
        //Récupère le dernier élément du tableau (ici le nom de la classe)
        //Ex: NomDeLaClasseMapper
        $part = array_pop($parts);
        //Récupère le nom de la classe en enlevant le mot 'Mapper'
        $part = substr($part, 0, -6);

        return "Application_Model_DbTable_$part";
    }

    //Permet de récupérer tous les résultats d'une table
    public function findAll()
    {
        try {
            $rowset = $this->getDbTable()->fetchAll();
        } catch (Zend_Db_Exception $e) {
            throw new Spesx_Mapper_Exception(
                    'Mapper : Echec FetchAll',
                    $e->getCode(),
                    $e);
        }
        return $this->_createItemsFromRowset($rowset);
    }

    //Permet de récupérer un résultat
    public function find($id)
    {
        try {
            $result = $this->getDbTable()->find($id);
        } catch (Zend_Db_Exception $e) {
            throw new Spesx_Mapper_Exception(
                    ' Mapper : echec Find ',
                    $e->getCode(),
                    $e);
        }
        if (0 == count($result)) {
            return;
        }
        $return = $this->_createItemFromRow($result->current());
        return $return;
    }

    //Permet de récupérer une source de données
    public function getDbTable()
    {
        //Si la source est nulle, on l'initialise
        if (null === $this->_dbTable) {
            $dbTableName = $this->_getDbTableName();
            $this->setDbTable($dbTableName);
        };
        return $this->_dbTable;
    }

    //Permet d'ajouter ou de modifier un tuple d'un table
    public function save($item, $id)
    {
        /* Récupère les données de l'objet (via les getters) sous forme
          d'un tableau */
        $data = $this->_getDataArrayFromItem($item);
        /* Initialise le nom de la méthode permettant de récupérer
          la clé primaire */
        $method = 'get_' . $id;

        if (null === ($item->$method())) {
            /* Si l'objet est null (inexistant dans la table de la BDD)
              on l'ajoute */
            try {
                $this->getDbTable()->insert($data);
            } catch (Zend_Db_Exception $e) {
                throw new Spesx_Mapper_Exception(
                        'Mapper: Echec Insertion methode save',
                        $e->getCode(),
                        $e);
            }
        } else {
            //S'il existe déjà dans la table de la BDD, on le modifie
            try {
                $where = array($id . ' = ?' => $item->{$method}());
                $this->getDbTable()->update($data, $where);
            } catch (Zend_Db_Exception $e) {
                throw new Spesx_Mapper_Exception(
                        'Mapper: Echec Update methode save',
                        $e->getCode(),
                        $e);
            }
        }
    }

    //Permet d'initialiser une source de données
    public function setDbTable($dbTableP)
    {
        //Vérifie s'il s'agit bien d'une chaine de caractères
        if (is_string($dbTableP)) {
            //Initialise la source de données
            try {
                $dbTable = new $dbTableP();
            } catch (Zend_Db_Exception $e) {
                throw new Spesx_Mapper_Exception(
                        'Mapper: Echec Initialisation dbTable, nom dbTable :' . $this->_getDbTableName() . ', ' . $e->getMessage(),
                        $e->getCode(),
                        $e);
            }
        }
        /* Vérifie qu'il s'agit bien d'une instance de la classe
          Zend_Db_Table_Abstract */
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Application_Exception(
                    'Passerelle vers la base de données invalide'
            );
        }
        $this->_dbTable = $dbTable;

        return $this;
    }

    public function delete($col, $val)
    {
        try {
            $where = $col . "='" . $val . "'";
            $this->getDbTable()->delete($where);
        } catch (Zend_Db_Exception $e) {
            throw new Spesx_Mapper_Exception(
                    'Mapper: Echec methode delete' . $e->getMessage(),
                    $e->getCode(),
                    $e);
        }
    }

    public function saveByLabel($item, $id)
    {
        $data = $this->_getDataArrayFromItem($item);
        $method = 'get_' . $id;
        if ($this->find($item->$method()) === null) {
            try {
                $this->getDbTable()->insert($data);
            } catch (Zend_Db_Exception $e) {
                throw new Spesx_Mapper_Exception(
                        'Mapper: Echec Insertion methode save',
                        $e->getCode(),
                        $e);
            }
        } else {
            try {
                $where = array($id . ' = ?' => $item->{$method}());
                $this->getDbTable()->update($data, $where);
            } catch (Zend_Db_Exception $e) {
                throw new Spesx_Mapper_Exception(
                        'Mapper: Echec Update methode save',
                        $e->getCode(),
                        $e);
            }
        }
    }

}
