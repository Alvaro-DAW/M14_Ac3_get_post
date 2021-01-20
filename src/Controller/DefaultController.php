<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
  /*
  public function index()
  {
    return new Response('Hola que tal');
  }*/

  /**
   * @Route("/", name="home")
   */
  public function home()
  {
    return $this->render('default/home.html.twig');
  }
 

}
