<?php
class A
{
  public function test()
  {
    return 'test';
  }
}

class B extends A
{
  public function test()
  {
    $retour->test();
    
    echo $retour;
  }
}

$b = new B;
$b->test(); // Affiche "test"