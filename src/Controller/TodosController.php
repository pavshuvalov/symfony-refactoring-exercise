<?php

namespace App\Controller;

use App\Entity\Todos;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class TodosController extends MyController
{
    public function getTodos()
    {
        $r = $this->getRequest();
        $all = $r->query->getInt('all', 0);
        $offset = $r->query->getInt('offset', 0);
        $length = $r->query->getInt('length', 20);

        // do not work with bad parameters
        if ($offset < 0) {
            return $this->error(['message' => 'offset < 0']);
        }
        if ($length < 1 || $length > 200) {
            return $this->error(['message' => 'length not in 1..200']);
        }

        $r = $this->getDoctrine()->getRepository(Todos::class);
        if ($all == 1) {
            $todos = $r->findNext($offset, $length);
        } else {
            $todos = $r->findNextByCompleted(false, $offset, $length);
        }

        $response = [
            'todos' => [],
            'has_next' => count($todos) == $length,
        ];
        foreach ($todos as $v) {
            $response['todos'][] = [
                'id' => $v->getId(),
                'text' => $v->getText(),
                'completed' => $v->getCompleted(),
            ];
        }
        return $this->ok($response);
    }

    public function tryChangeTodo(Request $request, int $id)
    {
        // nothing to change
        if (!$this->getRequest()->request->has('completed'))
        {
            return $this->ok();
        }

        $completed = $this->getRequest()->request->getBoolean('completed');
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository(Todos::class)->find($id);
        $todo->setCompleted($completed);
        $em->flush();
        return $this->ok();
    }
}
