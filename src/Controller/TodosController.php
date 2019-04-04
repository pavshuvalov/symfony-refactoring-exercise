<?php

namespace App\Controller;

use App\Entity\Todos;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TodosController extends AbstractController
{
    public function showTodos()
    {
        if (isset($_GET['all']) && $_GET['all'] == '1') {
            $r = $this->getDoctrine()->getRepository(Todos::class);
            $todos = $r->findNext(0, 20);
        } else {
            $r = $this->getDoctrine()->getRepository(Todos::class);
            $todos = $r->findNextByCompleted(false, 0, 20);
        }

        return $this->render('showTodos.html.twig', ['todos' => $todos]);
    }

    public function completeTodo()
    {
        $r = $this->getDoctrine()->getManager()->getRepository(Todos::class);
        $todo = $r->find($_GET['id']);
        $todo->setCompleted(true);
        $todo->flush();

        return $this->redirect('/');
    }

    public function uncompleteTodo()
    {
        $r = $this->getDoctrine()->getManager()->getRepository(Todos::class);
        $todo = $r->find($_GET['id']);
        $todo->setCompleted(false);
        $todo->flush();

        return $this->redirect('/');
    }
}
