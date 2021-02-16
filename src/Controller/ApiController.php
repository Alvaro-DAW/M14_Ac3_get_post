<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use App\Entity\Producte;
use App\Entity\Categoria;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ApiController extends AbstractFOSRestController
{

  /**
  * GET Route annotation.
  * @Get("/api/productes", name="get_productes")
  */
  public function getProductes()
  {
      $repository = $this->getDoctrine()->getRepository(Producte::class);
      // look for *all* Product objects
      $productes = $repository->findAll();

// el camp imatge que hem de subministrar al client es la URL que serveix la imatge
      foreach ($productes as $producte) {
        $producte->setBrochureFilename('http://localhost/M14/M14_Ac3/public/index.php/api/producteImatge/'.$producte->getId());
      }
      
      $data = $productes;

// construim la resposta HTTP i l'enviem
      $view = $this->view($data, 200);
      $view->setFormat('json');
      return $this->handleView($view);
  }

  /**
  * GET Route annotation.
  * @Get("/api/producteImatge/{id}")
  */
  public function getProducteImatge($id)
  {
      $repository = $this->getDoctrine()->getRepository(Producte::class);
      $producte = $repository->find($id);
      $file = $this->getParameter('photos_directory') . '/' . $producte->getBrochureFilename();
      //http://symfony.com/doc/current/components/http_foundation.html#serving-files
      $response = new BinaryFileResponse($file);
      $response->headers->set('Content-Type', 'image/jpeg');

      return $response;
  }
   /**
  * GET Route annotation.
  * @Get("/api/producte/{id}")
  */
  public function getProducte($id)
  {
    $repository = $this->getDoctrine()->getRepository(Producte::class);
    // look for *all* Product objects
    $producte = $repository->findBy(['id'=>$id]);

// el camp imatge que hem de subministrar al client es la URL que serveix la imatge
// construim la resposta HTTP i l'enviem
    $view = $this->view($producte, 200);
    $view->setFormat('json');
    return $this->handleView($view);
}

    /**
    * POST Route annotation.
    * @Post("/api/producte")
    */
    public function insertProducte(Request $request,SluggerInterface $slugger){
        try{
            $nomProducte = $request->request->get('nom');
            $descripcio = $request->request->get('descripcio');
            $valoracio = $request->request->get('valoracio');
            $imatgeProducte = $request->files->get('imatge');
            $categoriaProducte=$request->request->get('categoria');
            $categoria= $this->getDoctrine()
            ->getRepository(Categoria::class)
            ->find($categoriaProducte);
            
            $producte=new Producte();
            $producte
                ->setNom($nomProducte)
                ->setDescripcio($descripcio)
                ->setValoracio($valoracio)
                ->setCategoria($categoria);
                
                if ($imatgeProducte) {
                    $originalFilename = pathinfo($imatgeProducte->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imatgeProducte->guessExtension();

                    $imatgeProducte->move(
                            $this->getParameter('photos_directory'),
                            $newFilename
                    );

                    $producte->setBrochureFilename($newFilename);
    
                }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($producte);
            $entityManager->flush();

            $view = $this->view($producte, 200);
            $view->setFormat('json');
            return $this->handleView($view);
  
        }catch(\Exception $e){
            throw $e;
        }
       
    }
  }
