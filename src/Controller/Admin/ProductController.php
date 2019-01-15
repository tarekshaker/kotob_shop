<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 12/31/2018
 * Time: 2:48 AM
 */


namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;




class ProductController extends AbstractController
{

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * ProductController constructor.
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/admin/products" ,name="list_products",methods="GET")
     */
    public function getProductsList()
    {
        $products = $this->productService->getAllProducts();
        return $this->render('admin/products/list.html.twig', array('products' => $products));
    }


    /**
     * @Route("/admin/products/add_product",name="add_product",methods="GET|POST")
     */
    public function add_product(Request $request) : Response
    {

        $product = new Product();
        $product_form = $this->createForm(ProductFormType::class,$product);

        $product_form->handleRequest($request);

        if ($product_form->isSubmitted() && $product_form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success','The product added successfully');
            return $this->redirectToRoute('list_products');
        }

        return $this->render('admin/products/add.html.twig', array('form' => $product_form->createView()));


    }


    /**
     * @Route("/admin/products/edit_product/{id}",name="edit_product", methods="GET|POST")
     */
    public function edit_product(Request $request,$id) : Response
    {
        $product = new Product();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $product_form = $this->createForm(ProductFormType::class,$product);


        $product_form->handleRequest($request);

        if ($product_form->isSubmitted() && $product_form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success','The product updated successfully');
            return $this->redirectToRoute('list_products');
        }

        return $this->render('admin/products/edit.html.twig', array('form' => $product_form->createView()));
    }


    /**
     * @Route("/admin/products/delete_product/{id}",name="delete_product", methods="DELETE")
     */
    public function delete_product(Request $request, $id) :Response
    {

        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success','The product deleted successfully');

        $response = new Response();
        $response->send();

    }

    /**
     * @Route("/admin/products/show_product/{id}",name="show_product", methods="GET")
     */
    public function show_product(Request $request, $id) :Response
    {

        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render('admin/products/show.html.twig', array('product' => $product));


    }
}