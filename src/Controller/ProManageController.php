<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProSize;
use App\Form\ProductType;
use App\Form\ProSizeType;
use App\Repository\ProductRepository;
use App\Repository\ProSizeRepository;
use App\Repository\SizeRepository;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Json;

/**
 * @Route("/admin/product")
 */

class ProManageController extends AbstractController
{
    private ProductRepository $repo;
    public function __construct(ProductRepository $repo)
    {
        $this->repo = $repo;
    }

    /* ==========================================================================
   Show Product Page
   ========================================================================== */

    /**
     * @Route("/", name="pro_page")
     */
    public function productMangeAction(): Response
    {
        $products = $this->repo->findAll([], [
            'id' => 'DESC'
        ]);
        // $size = $this->repo->findAllSize();

        // return $this->json($size);
        return $this->render('pro_manage/index.html.twig', [
            'products' => $products,
            // 'size' => $size
        ]);
    }

    /* ==========================================================================
   Add Admin Product Page
   ========================================================================== */

    /**
     * @Route("/create", name="addPro_page")
     */
    public function createAction(Request $req, SluggerInterface $slugger, ManagerRegistry $reg): Response
    {
        $p = new Product();
        $productForm = $this->createForm(ProductType::class, $p);

        $productForm->handleRequest($req);
        $entity = $reg->getManager();

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $data = $productForm->getData($req);
            $p->setName($data->getName());
            $p->setDescriptions($data->getDescriptions());
            $p->setPrice($data->getPrice());
            $p->setStatus(1);
            $p->setImage($data->getImage());
            // $p->setForGender($data->isForGender());
            $forGender = $req->request->get('forGender'); // Lấy giá trị từ request

            // Kiểm tra giá trị và gán mặc định nếu null
            if ($forGender === null) {
                $forGender = false; // Hoặc gán giá trị mặc định true/false
            }

            $p->setForGender((bool) $forGender); // Ép kiểu về bool trước khi truyền  
            $p->setCategory($data->getCategory());
            $p->setSupplier($data->getSupplier());

            $imgFile = $productForm->get('image')->getData();

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

            $p->setImage($newFilename);
            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entity->persist($p);
            // actually executes the queries (i.e. the INSERT query)
            $entity->flush();
            

            return $this->redirectToRoute("addProSize_page", [
                'id' => $p->getId()
            ]);
        }

        return $this->render('pro_manage/new.html.twig', [
            'productForm' => $productForm->createView()
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
   

    /* ==========================================================================
   Update Admin Product Page
   ========================================================================== */

    /**
     * @Route("/edit/{id}", name="editPro_page")
     */

    public function editAction(Request $req, SluggerInterface $slugger, ManagerRegistry $reg, int $id): Response
    {
        $p = $this->repo->find($id);
        $entity = $reg->getManager();

        $productForm = $this->createForm(ProductType::class, $p);
        $productForm->handleRequest($req);
        // $entity = $reg->getManager();

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $data = $productForm->getData($req);
            $p->setName($data->getName());
            $p->setDescriptions($data->getDescriptions());
            $p->setPrice($data->getPrice());
            $p->setStatus($data->isStatus());
            $p->setImage($data->getImage());
            $p->setForGender($data->isForGender());
            $p->setCategory($data->getCategory());
            $p->setSupplier($data->getSupplier());

            $imgFile = $productForm->get('image')->getData();

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
            $p->setImage($newFilename);

            $entity->persist($p);
            $entity->flush();

            return $this->redirectToRoute("pro_page");
        }

        return $this->render('pro_manage/edit.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }

    // http://127.0.0.1:8000/admin/product/delete/3 

    /* ==========================================================================
   Delete Admin Product Page
   ========================================================================== */

    // /**
    //  * @Route("/delete/{id}", name="deletePro_page", methods={"DELETE"})
    //  */
    // public function Action(int $id): Response
    // {

    //     $product = $this->repo->find($id);
    //     // $this->repo->remove($product, true);
    //     try {
    //         $this->repo->remove($product, false);
    //         $this->repo->flush();
    //     } catch (\Exception $e) {
    //         // Log or handle the exception
    //         echo 'Caught exception: ',  $e->getMessage(), "\n";
    //     }
    //     return new JsonResponse();
    // }
    /**
     *  @Route("/delete/{id}", name="product_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Request $req): Response
    {
        $id = $req->get('id');
        $c = $this->repo->find($id);
        try{
            $this->repo->remove($c, true);
        }
       catch( ForeignKeyConstraintViolationException $e){
            return $this->render("pro_manage/error.html.twig", [
                'message' => "Can not remove"
            ]);
       }
        return $this->redirectToRoute('pro_page', [], Response::HTTP_SEE_OTHER);
    }
}
