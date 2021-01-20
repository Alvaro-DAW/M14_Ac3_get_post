<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Producte;
use App\Entity\Categoria;
use App\Repository\ProducteRepository;
use App\Form\ProducteType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;


class ProducteController extends AbstractController
{
    /**
     * @Route("/producte", name="producte")
     */
    public function index(): Response
    {
        return $this->render('producte/index.html.twig', [
            'controller_name' => 'ProducteController',
        ]);
    }
     /**
     * @Route("/producte/list", name="producte_list")
     */
    public function list(ProducteRepository $producteRepository)
    {
        $categories = $this->getDoctrine()
            ->getRepository(Categoria::class)
            ->findAll();
        //$productes=$producteRepository->findAll();
        $productes = $this->getDoctrine()
            ->getRepository(Producte::class)
            ->findAll();
       
        //codi de prova per visualitzar l'array de producte
        // dump($productes);
        // exit();

        return $this->render('producte/list.html.twig', ['productes' => $productes,'categories'=>$categories]);
    }
    /**
     * @Route("/producte/edit/{id<\d+>}", name="producte_edit")
     */
    public function edit($id, Request $request)
    {
        $producte = $this->getDoctrine()
            ->getRepository(Producte::class)
            ->find($id);

        //fent això el text del boto submit tindria el valor per defecte 'Enviar'
        //$form = $this->createForm(producteType::class, $producte);

        //podem personalitzar el text del botó passant una opció 'submit' al builder de la classe producteType 
        // http://www.keganv.com/passing-arguments-controller-file-type-symfony-3/
        $form = $this->createForm(ProducteType::class, $producte, array('submit'=>'Desar'));
        
        //també ho podríem fer d'una altra manera: sobreescriure el botó save 
        /*$form = $this->createForm(producteType::class, $producte);
        $form->add('save', SubmitType::class, array('label' => 'Desar'));*/

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $producte = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($producte);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'producte '.$producte->getNom().' desada!'
            );

            return $this->redirectToRoute('producte_list');
        }

        return $this->render('producte/producte.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Editar producte',
        ));
    }

    /**
     * @Route("/producte/delete/{id<\d+>}", name="producte_delete")
     */
    public function delete($id, Request $request)
    {
        try{
            $producte = $this->getDoctrine()
            ->getRepository(Producte::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $nomproducte = $producte->getNom();
        $entityManager->remove($producte);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'producte '.$nomproducte.' eliminada!'
        );

        return $this->redirectToRoute('producte_list');
        }catch(\Exception $e){
            return $this->render('producte/error.html.twig');
        }
       
    }
  
    /**
     * @Route("/producte/filter", name="producte_filter")
     */
    public function filter(Request $request)
    {
        //recollim el paràmetre 'term' enviat per post
        $term = $request->request->get('term');
        
        $categories = $this->getDoctrine()
            ->getRepository(Categoria::class)
            ->findAll();

        $categoria= $this->getDoctrine()
            ->getRepository(Categoria::class)
            ->findByNom($term);
        
        if ($term == null) {
            $producte = $this->getDoctrine()
            ->getRepository(Producte::class)
            ->findAll();
        }else{
            $producte = $this->getDoctrine()
            ->getRepository(Producte::class)
            ->findByCategoria($categoria);

        }
       
        
        return $this->render('producte/list.html.twig', [
            'productes' => $producte,
            'searchTerm' => $term,
            'categories' => $categories
        ]);
    }

      /**
     * @Route("/producte/new", name="producte_new")
     */
    public function new(Request $request, SluggerInterface $slugger)
    {
        $producte = new Producte();

        //sense la classe producteType faríem:
        /*$form = $this->createFormBuilder($producte)
            ->add('nom', TextType::class)
            ->add('prioritat', IntegerType::class)
            ->add('completada', CheckboxType::class)
            ->add('save', SubmitType::class, array('label' => 'Crear producte'))
            ->getForm();*/

        //podem personalitzar el text del botó passant una opció 'submit' al builder de la classe producteType 
        $form = $this->createForm(ProducteType::class, $producte, array('submit'=>'Crear Producte'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           
            $producte = $form->getData();

            
           

              /** @var UploadedFile $brochureFile */
              $brochureFile = $form->get('brochure')->getData();

             
              if ($brochureFile) {
                  $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                  $safeFilename = $slugger->slug($originalFilename);
                  $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
  
                  
                  try {
                      $brochureFile->move(
                          $this->getParameter('photos_directory'),
                          $newFilename
                      );
                  } catch (FileException $e) {
                      // ... handle exception if something happens during file upload
                  }
  
                  // updates the 'brochureFilename' property to store the PDF file name
                  // instead of its contents
                  $producte->setBrochureFilename($newFilename);



                 

                  $entityManager = $this->getDoctrine()->getManager();
                  $entityManager->persist($producte);
                  $entityManager->flush();
                  $this->addFlash(
                    'notice',
                    'Nou producte '.$producte->getNom().' creada!'
                );
              }

            return $this->redirectToRoute('producte_list');
        }

        return $this->render('producte/producte.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nou producte',
        ));
    }

    /**
     * 
     * 
     */
    public function producte(ValidatorInterface $validator){
    $Producte = new Producte();

    // ... do something to the $producte object

    $errors = $validator->validate($producte);

    if (count($errors) > 0) {
        /*
         * Uses a __toString method on the $errors variable which is a
         * ConstraintViolationList object. This gives us a nice string
         * for debugging.
         */
        $errorsString = (string) $errors;

        return new Response($errorsString);
    }

    return new Response('The author is valid! Yes!');
    }
}
