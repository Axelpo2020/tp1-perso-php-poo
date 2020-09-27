<?php
class Personnage
{
  private $_id,
  $_degats,
  $_nom;
//kkkk
  const CES_MOI = 1;
  const PERSONNAGE_TUE = 2;
  const PERSONNAGE_FRAPPE = 3;

  public function __construct(array $donnes)
  {
    $this->hydrate($donnes);
  }

  public function frapper(Personnages $perso)
  {
    //vérifier qu'on ne se tape pas soi-même
    //Si c'est le cas on stoppe tout en renvoyant une valeur signifiant que le personnage ciblé est le personnage qui attaque
    if ($this->_id == $perso->id()){
      return self::CEST_MOI;
    }

    //On indique aux personnages frappés qu'il doit recevoir des dégâts
    return $perso->recevoirDegats();
  }

  public function recevoirDegats()
  {
   //On augmente de 5 les dégâts
   $this->_degats += 5;

   //Si on a 100 de dégâts en plus la méthode renverra une valeur signifiant que le personnage a été tué
   if ($this->_degats >= 100){
     return self::PERSONNAGE_TUE;
   }

   //Sinon elle recevra une valeur signifiant que le personnage a bien été frappé
   return self::PERSONNAGE_FRAPPE;
  }

  public function nomValide()
  {
    return !empty($this->_nom);
  }

  //Hydratation
  public function hydrate(array $donnes)
  {
    foreach ($donnes as $key => $value)
    {
      //On récupére le nom du setter correspondant à l'attribut
      $method = 'set'.ucfirst($key);

      if (method_exists($this, $method))
      {
        $this->$method($value);
      }
    }
  }

  //GETTERS
  public function id(){ return $this->_id; }
  public function degats(){ return $this->_degats; }
  public function nom(){ return $this->_nom; }

  //SETTERS
  public function setId($id)
  {
    $id = (int) $id;
    if ($id > 0){
      return $this->_id = $id;
    }
  }

  public function setDegats($degats)
  {
    $degats = (int) $degats;
    if ($degats >= 0 && $degats <= 100){
      return $this->_degats = $degats;
    }
  }

  public function setNom($nom)
  {
    if (is_string($nom)){
      return $this->_nom = $nom;
    }
  }


}