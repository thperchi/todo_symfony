<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\TodolistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/addtask/{id}", name="addtask")
     */
    public function addtask(int $id, TodolistRepository $lists, Request $req, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('lists');
        }

        $list = $lists->findOneBy(['id' => $id]);
    
        $task = new Task();
        $task->setList($lists->findOneBy(['id' => $id]));
        $task->setDone(false);
    
        $form = $this->createForm(TaskType::class, $task);
    
        $form->handleRequest($req);
    
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();
        }
        return $this->render('task/addtask.html.twig', [
            'list' => $list,
            'tasks' => $list->getTasks(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/deletetask/{id}", name="deletetask")
     */
    public function deletetask(int $id, EntityManagerInterface $em, TaskRepository $tasks): Response
    {
        $task = $tasks->findOneBy(['id' => $id]);
        if($this->getUser() == $task->getList()->getUser()) {
            $em->remove($task);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('lists'));
    }

    /**
     * @Route("/updatetask/{id}", name="updatetask")
     */
    public function updatetask(int $id, EntityManagerInterface $em, Request $req, TaskRepository $tasks): Response
    {
        $task = $tasks->findOneBy(['id' => $id]);
        
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid() && $this->getUser() == $task->getList()->getUser()) {
            $em->persist($task);
            $em->flush();
            return $this->redirect($this->generateUrl('lists'));
        }
        return $this->render('task/updatetask.html.twig', [
            'task' => $task,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/validatetask/{id}", name="validatetask")
     */
    public function validatetask(int $id, EntityManagerInterface $em, TaskRepository $tasks): Response
    {
        $task = $tasks->findOneBy(['id' => $id]);
        if($this->getUser() == $task->getList()->getUser() && $this->getUser() == $task->getList()->getUser()) {
            if ($task->getDone() == false) $task->setDone(true);
            else $task->setDone(false);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('lists'));
    }
}
