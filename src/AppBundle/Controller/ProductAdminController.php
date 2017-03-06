<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductAdminController extends Controller
{
    /**
     * @Route("/admin/products", name="product_list")
     */
    public function listAction()
    {
        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/admin/products/new", name="product_new")
     */
    public function newAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Product created FTW!');

            $product = new Product();
            $product->setName("Veloci-chew toy");
            $product->setDescription("description");
            $product->setPrice(20);
            $product->setAuthor($this->getUser());
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/new.html.twig');
    }
}
