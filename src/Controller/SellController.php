<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sale;

class SellController extends AbstractController
{
    #[Route('/sell', name: 'app_sell_index')]
    public function index(): Response
    {
        return $this->render('sell/index.html.twig');
    }

    #[Route('/sell/create', name: 'app_sell_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        // Validate the incoming data
        if (!$data || !isset($data['products']) || !isset($data['discount'])) {
            return $this->json(['error' => 'Invalid data provided'], Response::HTTP_BAD_REQUEST);
        }

        // Process the sale (you'll need to define your Sale entity)
        $sale = new Sale();
        $sale->setDiscount($data['discount']);
        $sale->setProducts($data['products']); // You would likely need a more complex handling here

        $entityManager->persist($sale);
        $entityManager->flush();

        return $this->json(['message' => 'Sale created successfully']);
    }
}
