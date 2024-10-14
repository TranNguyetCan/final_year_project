<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/ingredient")
 */
class IngredientManageController extends AbstractController
{
    private IngredientRepository $repo;
    public function __construct(IngredientRepository $repo)
    {
        $this->repo = $repo;
    }
    /**
     * @Route("/", name="ingredient_page")
     */
    public function readAllAction(): Response
    {
        $i = $this->repo->findAll();
        return $this->render('ingredient_manage/index.html.twig', [
            'ingredient' => $i
        ]);
    }
      /**
     * @Route("/add", name="ingredient_create")
     */
    // public function createAction(Request $req, IngredientRepository $repo): Response
    // {

    //     $i = new Ingredient();
    //     $formIng = $this->createForm(IngredientType::class, $i);

    //     $formIng->handleRequest($req);
    //     if ($formIng->isSubmitted() && $formIng->isValid()) {


    //         $repo->save($i, true);
    //         return $this->redirectToRoute('ingredient_page', [], Response::HTTP_SEE_OTHER);
    //     }
    //     return $this->render("ingredient_manage/new.html.twig", [
    //         'formIng' => $formIng->createView()
    //     ]);
    // }
     
    public function createAction(Request $req, SluggerInterface $slugger,  ManagerRegistry $reg): Response
    {
        $i = new Ingredient();
        $formIng = $this->createForm(IngredientType::class, $i);

        $formIng->handleRequest($req);
        $entity = $reg->getManager();

        if ($formIng->isSubmitted() && $formIng->isValid()) {
            $data = $formIng->getData($req);
            $i->setName($data->getName());
            $i->setQuantity($data->getQuantity());
            $i->setUnit($data->getUnit());
            $i->setPrice($data->getPrice());
            $i->setInventory($data->getInventory());
            $i->setImage($data->getImage());

            $imgFile = $formIng->get('image')->getData();

            if ($imgFile && $imgFile != "") :
                // Rename File Imange
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                //  SluggerInterface $slugger
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();
            endif;

            // Services: thiết lập biến mt
            try {
                $imgFile->move(
                    $this->getParameter('image_dir'),
                    $newFilename
                );
            } catch (FileException $th) {
                echo $th;
            }

            $i->setImage($newFilename);
            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entity->persist($i);
            // actually executes the queries (i.e. the INSERT query)
            $entity->flush();
            

            return $this->redirectToRoute("ingredient_page", [
                
            ]);
        }

        return $this->render('ingredient_manage/new.html.twig', [
            'formIng' => $formIng->createView()
        ]);
    }

    public function uploadImage($imgFile, SluggerInterface $slugger): ?string
    {
        $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();
        try {
            $imgFile->move(
                $this->getParameter('image_dir'),
                $newFilename
            );
        } catch (FileException $e) {
            echo $e;
        }
        return $newFilename;
    }
   
     /**
     * @Route("/edit/{id}", name="ingredient_edit")
     */
     public function editAction(Request $req, SluggerInterface $slugger, ManagerRegistry $reg, int $id): Response
     {
         $i = $this->repo->find($id);
         $entity = $reg->getManager();
 
         $formIng = $this->createForm(IngredientType::class, $i);
         $formIng->handleRequest($req);
         // $entity = $reg->getManager();
 
         if ($formIng->isSubmitted() && $formIng->isValid()) {
            $data = $formIng->getData($req);
            $i->setName($data->getName());
            $i->setQuantity($data->getQuantity());
            $i->setUnit($data->getUnit());
            $i->setPrice($data->getPrice());
            $i->setInventory($data->getInventory());
            $i->setImage($data->getImage());

            $imgFile = $formIng->get('image')->getData();

 
             if ($imgFile && $imgFile != "") :
                 // Rename File Imange
                 $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                 //  SluggerInterface $slugger
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();
             endif;
 
             try {
                 $imgFile->move(
                     $this->getParameter('image_dir'),
                     $newFilename
                 );
             } catch (FileException $th) {
                 echo $th;
             }
             $i->setImage($newFilename);
 
             $entity->persist($i);
             $entity->flush();
 
             return $this->redirectToRoute("ingredient_page");
         }
 
         return $this->render('ingredient_manage/edit.html.twig', [
             'formIng' => $formIng->createView()
         ]);
     }
    /**
     *  @Route("/delete/{id}", name="ingredient_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Request $req): Response
    {
        $id = $req->get('id');
        $i = $this->repo->find($id);

        try{
            $this->repo->remove($i, true);
        }
       catch(ForeignKeyConstraintViolationException $e){
            return $this->render("ingredient_manage/error.html.twig", [
                'message' => "Can not remove"
            ]);
       }
        return $this->redirectToRoute('ingredient_page', [], Response::HTTP_SEE_OTHER);
    }

}
