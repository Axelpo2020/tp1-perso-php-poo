<?php
// On enregistre notre autoload.

function chargerClasse($classname)
{
  require $classname.'.php';
}

spl_autoload_register('chargerClasse');

session_start();

if (isset($_GET['deconnexion']))
{
  session_destroy();
  header('Location: .');
  exit();
}

$db = new PDO('mysql:host=localhost;dbname=personnages', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.

$manager = new PersonnagesManager($db);
if (isset($_SESSION['perso'])) // Si la session perso existe, on restaure l'objet.
{
  $perso = $_SESSION['perso'];
}

//Si on a voulu creer un personnage 
if (isset($_POST['creer']) && isset($_POST['nom']))
{
    $perso = new Personnage(['nom' => $_POST['nom']]); //On creer un nouveau personnage

    if (!$perso->nomValide())
    {
        $message = 'Le nom choisi est invalide'; 
    }
    elseif ($manager->exists($perso->getNom()))
    {
        $message = 'Ce nom est déja utilisé';
        unset($perso); //destruction de la variable perso
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
        $perso = $manager->get($_POST['nom']);
    }
    else {
        $message = "Ce personnage n'existe pas";
    }
}

elseif (isset($_GET['frapper']))
{
  if (!isset($perso)) {
    $message = 'Merci de créer un personnage ou de vous identifier.';
  }
  else {
    if (!$manager->exists((int) $_GET['frapper']))
    {
      $message = 'Le personnage que vous voulez frapper n\'existe pas !';
    }
    else {
      $persoAFrapper = $manager->get((int) $_GET['frapper']);

      $retour = $perso->frapper($persoAFrapper); //On stocke en retour les éventuelles erreurs au message que renvoie la méthode frapper

      switch ($retour)
      {
        case Personnage::CES_MOI :
          $message = 'Mais... pourquoi voulez-vous vous frapper ???';
          break;
        
        case Personnage::PERSONNAGE_FRAPPE :
          $message = 'Le personnage a bien été frappé !';
          
          $manager->update($perso);
          $manager->update($persoAFrapper);
          
          break;
        
        case Personnage::PERSONNAGE_TUE :
          $message = 'Vous avez tué ce personnage !';
          
          $manager->update($perso);
          $manager->delete($persoAFrapper);
          
          break;
      }
    }
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

  <?php
  if (isset($message)) {
    echo '<p>', $message, '</p>';
  }


  if (isset($perso)) // Si on utilise un personnage (nouveau ou pas).
  {
  ?>

  <p><a href="?deconnexion=1">Déconnexion</a></p>
      <fieldset>
        <legend>Mes informations</legend>
        <p>
          Nom : <?= htmlspecialchars($perso->getNom()) ?><br />
          Dégâts : <?= $perso->getDegats() ?>
        </p>
      </fieldset>
      
      <fieldset>
        <legend>Qui frapper ?</legend>
        <p>
  <?php
  $persos = $manager->getList($perso->getNom());
  if (empty($persos)) {
    echo 'Personne à frapper !';
  } else {
    foreach ($persos as $unPerso)
    echo '<a href="?frapper=', $unPerso->getId(), '">', htmlspecialchars($unPerso->getNom()), '</a> (dégâts : ', $unPerso->getDegats(), ')<br />';
  }
  ?>
        </p>
      </fieldset>
  <?php
  }
  else
  {
  ?>

  
  <p>Nombre de personnage créés : <?= $manager->count() ?></p>
    <form action="" method="post">
      <p>
        Nom : <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Créer ce personnage" name="creer" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" />
      </p>
    </form>
    <?php
}
?>
  </body>
</html>
<?php
if (isset($perso))
{
  $_SESSION['perso'] = $perso;
}