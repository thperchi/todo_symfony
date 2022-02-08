<?php

namespace App\Controller;

use App\Entity\Todolist;
use App\Form\TodolistType;
use App\Repository\TodolistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @Route("/addlist", name="addlist")
     */
    public function addlist(EntityManagerInterface $em, Request $req): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('lists');
        }

        $list = new Todolist();
        $list->setUser($this->getUser());
        
        $form = $this->createForm(TodolistType::class, $list);

        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($list);
            $em->flush();
            return $this->redirect($this->generateUrl('lists'));
        }
        return $this->render('list/addlist.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/deletelist/{id}", name="deletelist")
     */
    public function deletelist(int $id, EntityManagerInterface $em, TodolistRepository $lists): Response
    {
        $list = $lists->findOneBy(['id' => $id]);

        if($this->getUser() == $list->getUser()) {
            $em->remove($list);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('lists'));
    }

    /**
     * @Route("/updatelist/{id}", name="updatelist")
     */
    public function updatelist(int $id, EntityManagerInterface $em, Request $req, TodolistRepository $lists): Response
    {
        $list = $lists->findOneBy(['id' => $id]);
        
        if ($this->getUser() != $list->getUser()) {
            return $this->redirectToRoute('lists');
        }

        $form = $this->createForm(TodolistType::class, $list);
        
        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid() && $this->getUser() == $list->getUser()) {
            $em->persist($list);
            $em->flush();
            return $this->redirect($this->generateUrl('lists'));
        }
        return $this->render('list/updatelist.html.twig', [
            'list' => $list,
            'form' => $form->createView()
        ]);
    }
}