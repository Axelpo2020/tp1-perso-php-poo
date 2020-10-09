<?php
// On enregistre notre autoload.
function chargerClasse($classname)
{
  require $classname.'.php';
}

spl_autoload_register('chargerClasse'); 

$db = new PDO('mysql:host=localhost;dbname=personnages;charset=utf8', 'root', 'root');
$manager = new PersonnagesManager($db);

//Si on a voulu creer un personnage 
if (isset($_POST['creer']) && isset($_POST['nom']))
{
    $perso = new Personnage(['nom'] => $_POST['nom']);

    if (!$perso->nomValide())
    {
        $message = 'Le nom choisi est invalide'; 
    }
    elseif ($manager->exists($perso->nom()))
    {
        $message = 'Ce nom est déja utilisé';
        unset($perso) //destruction de la variable perso
    }
    else 
    {
        $manager->add($perso);
    }
}

//Si on a voulut utiliser ce personnage 
elseif (isset($_POST['utiliser']) && isset($_POST['nom']))
{
    //Si celui ci existe
    if ($manager->exists($_POST['nom']))
    {
        $perso->get($_POST['nom']);
    }
    else {
        $message = "Ce personnage n'existe pas";
    }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>TP : Mini jeu de combat</title>
    
    <meta charset="utf-8" />
  </head>
  <body>
    <form action="" method="post">
      <p>
        Nom : <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Créer ce personnage" name="creer" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" />
      </p>
    </form>
  </body>
</html>