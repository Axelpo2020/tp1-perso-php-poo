<?php

class PersonnagesManager 
{
    private $_db;

    public function __construct($db)
    {
        $this->setDb($db);
    }

    public function add(Personnage $perso)
    {
        $q = $this->_db->prepare('INSERT INTO personnages(nom) VALUES(:nom');
        $q->bindValue(':nom', $perso->nom());
        $q->execute();

        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
        ]);
    }

    public function count()
    {
        return $this->_db->prepare('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }


    public function setDb(PDO $db) 
    {
        $this->$_db;
    }
}