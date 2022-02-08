<?php

namespace App\Controller;

use App\Repository\TodolistRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="lists")
     */
    public function index(TodolistRepository $lists): Response
    {
        return $this->render('main/index.html.twig', [
            'lists' => $lists->findAll()
        ]);
    }

    /**
     * @Route("/userlists/{id}", name="userlists")
     */
    public function userlist(int $id, UserRepository $users): Response
    {
        $user = $users->findOneBy(['id' => $id]);

        return $this->render('main/userlists.html.twig', [
            'lists' => $user->getTodolists()
        ]);
    }
}
