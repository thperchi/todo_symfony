<?php

namespace App\Controller;

use App\Repository\TodolistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/lists", name="lists")
     */
    public function index(TodolistRepository $lists): Response
    {
        return $this->render('main/index.html.twig', [
            'lists' => $lists->findAll()
        ]);
    }
}
