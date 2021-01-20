<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use App\Form\CategoriaType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class CategoriaController extends AbstractController
{
   /**
     * @Route("/categoria", name="categoria")
     */
    public function index(): Response
    {
        return $this->render('categoria/index.html.twig', [
            'controller_name' => 'categoriaController',
        ]);
    }
     /**
     * @Route("/categoria/list", name="categoria_list")
     */
    public function list()
    {
        //$categorias=$categoriaRepository->findAll();
        $categorias = $this->getDoctrine()
            ->getRepository(Categoria::class)
            ->findAll();
       
        //codi de prova per visualitzar l'array de categoria
        // dump($categorias);
        // exit();

        return $this->render('categoria/list.html.twig', ['categorias' => $categorias]);
    }
    /**
     * @Route("/categoria/edit/{id<\d+>}", name="categoria_edit")
     */
    public function edit($id, Request $request)
    {
        $categoria = $this->getDoctrine()
            ->getRepository(categoria::class)
            ->find($id);

        //fent això el text del boto submit tindria el valor per defecte 'Enviar'
        //$form = $this->createForm(categoriaType::class, $categoria);

        //podem personalitzar el text del botó passant una opció 'submit' al builder de la classe categoriaType 
        // http://www.keganv.com/passing-arguments-controller-file-type-symfony-3/
        $form = $this->createForm(categoriaType::class, $categoria, array('submit'=>'Desar'));
        
        //també ho podríem fer d'una altra manera: sobreescriure el botó save 
        /*$form = $this->createForm(categoriaType::class, $categoria);
        $form->add('save', SubmitType::class, array('label' => 'Desar'));*/

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoria = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categoria);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'categoria '.$categoria->getNom().' desada!'
            );

            return $this->redirectToRoute('categoria_list');
        }

        return $this->render('categoria/categoria.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Editar categoria',
        ));
    }

    /**
     * @Route("/categoria/delete/{id<\d+>}", name="categoria_delete")
     */
    public function delete($id, Request $request)
    {
        try{
            $categoria = $this->getDoctrine()
            ->getRepository(categoria::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $nomcategoria = $categoria->getNom();
        $entityManager->remove($categoria);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'categoria '.$nomcategoria.' eliminada!'
        );

        return $this->redirectToRoute('categoria_list');
        }catch(\Exception $e){
            return $this->render('categoria/error.html.twig');
        }
       
    }
     /**
     * @Route("/categoria/search", name="categoria_search")
     */
    public function search(Request $request)
    {
        //recollim el paràmetre 'term' enviat per post
        $term = $request->request->get('term');

        $categoria = $this->getDoctrine()
            ->getRepository(categoria::class)
            ->findLikeNom($term);

        return $this->render('categoria/list.html.twig', [
            'categoria' => $categoria,
            'searchTerm' => $term,
        ]);
    }

      /**
     * @Route("/categoria/new", name="categoria_new")
     */
    public function new(Request $request)
    {
        $categoria = new categoria();

        //sense la classe categoriaType faríem:
        /*$form = $this->createFormBuilder($categoria)
            ->add('nom', TextType::class)
            ->add('prioritat', IntegerType::class)
            ->add('completada', CheckboxType::class)
            ->add('save', SubmitType::class, array('label' => 'Crear categoria'))
            ->getForm();*/

        //podem personalitzar el text del botó passant una opció 'submit' al builder de la classe categoriaType 
        $form = $this->createForm(categoriaType::class, $categoria, array('submit'=>'Crear categoria'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoria = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categoria);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Nou categoria '.$categoria->getNom().' creada!'
            );

            return $this->redirectToRoute('categoria_list');
        }

        return $this->render('categoria/categoria.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nou categoria',
        ));
    }

    /**
     * 
     * 
     */
    public function categoria(ValidatorInterface $validator){
    $categoria = new categoria();

    // ... do something to the $categoria object

    $errors = $validator->validate($categoria);

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
