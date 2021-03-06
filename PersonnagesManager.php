<?php

class PersonnagesManager 
{
    private $_db;

    public function __construct($db)
    {
        $this->setDb($db);
    }

    public function setDb(PDO $db) 
    {
        $this->_db = $db;
    }
    
    public function add(Personnage $perso)
    {
        $q = $this->_db->prepare('INSERT INTO personnages(nom) VALUES(:nom)');
        $q->bindValue(':nom', $perso->getNom());
        $q->execute();

        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
        ]);
    }

    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }

    public function delete(Personnage $perso)
    {
        $this->_db->query('DELETE FROM personnages WHERE id = ' .  $perso->getId());
    }

    public function exists($info)
    {
        //Recherche du perso grace à son id
        if (is_int($info))
        {
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
        }
        //Recherche du perso grace à sn nom
        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
        $q->execute([':nom' => $info]);

        return (bool) $q->fetchColumn();

    }

    public function get($info)
    {
      if (is_int($info))
      {
        $q = $this->_db->query('SELECT id, nom, degats FROM personnages WHERE id = '.$info);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        
        return new Personnage($donnees);
      }
      else
      {
        $q = $this->_db->prepare('SELECT id, nom, degats FROM personnages WHERE nom = :nom');
        $q->execute([':nom' => $info]);
      
        return new Personnage($q->fetch(PDO::FETCH_ASSOC));
      }
    }

    
    public function update(Personnage $perso)
    {
      $q = $this->_db->prepare('UPDATE personnages SET degats = :degats WHERE id = :id');
      
      $q->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
      $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);
      
      $q->execute();
    }
    public function getList($nom)
    {
      $persos = [];
      
      $q = $this->_db->prepare('SELECT id, nom, degats FROM personnages WHERE nom <> :nom ORDER BY nom');
      $q->execute([':nom' => $nom]);
      
      while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
      {
        $persos[] = new Personnage($donnees);
      }
      
      return $persos;
    }
}