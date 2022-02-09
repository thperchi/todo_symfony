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
    
        $isInvited = false;
        foreach ($list->getInvited() as $i) {
            if ($i == $this->getUser()) $isInvited = true;
        }

        $task = new Task();
        $task->setList($lists->findOneBy(['id' => $id]));
        $task->setDone(false);
    
        $form = $this->createForm(TaskType::class, $task);
    
        $form->handleRequest($req);
    
        if($form->isSubmitted() && $form->isValid() && ($this->getUser() == $list->getUser() || $isInvited == true)) {
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

        $isInvited = false;
        foreach ($task->getList()->getInvited() as $i) {
            if ($i == $this->getUser()) $isInvited = true;
        }

        if ($this->getUser() == $task->getList()->getUser()) $isInvited = true;
        if ($this->getUser() != $task->getList()->getUser() && $isInvited == false) {
            return $this->redirectToRoute('lists');
        }
        
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid() && ($this->getUser() == $task->getList()->getUser() || $isInvited == true)) {
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

        $isInvited = false;
        foreach ($task->getList()->getInvited() as $i) {
            if ($i == $this->getUser()) $isInvited = true;
        }
        if($this->getUser() == $task->getList()->getUser() || $isInvited == true) {
            if ($task->getDone() == false) $task->setDone(true);
            else $task->setDone(false);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('lists'));
    }
}
