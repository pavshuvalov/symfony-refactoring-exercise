<?php

namespace App\Controller;

use App\Entity\Todos;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

class TodosController extends MyController
{
    // -------------------------------------------------------
    // Controller Antispam Constants
    // -------------------------------------------------------

    // 20 times per 5 minutes
    private const _GET_TODOS_BLOCK = [
        'key'    => 'GET_TODOS_BLOCK',
        'expire' => 5 * 60,
        'count'  => 20,
    ];

    // 20 times per 5 minutes
    private const _TRY_CHANGE_TODO_BLOCK = [
        'key'    => 'TRY_CHANGE_TODO_BLOCK',
        'expire' => 5 * 60,
        'count'  => 20,
    ];

    // -------------------------------------------------------
    // Public Controller Methods
    // -------------------------------------------------------

    public function getTodos(Request $r)
    {
        $all = $r->query->getInt('all', 0);
        $offset = $r->query->getInt('offset', 0);
        $length = $r->query->getInt('length', 20);

        // do not work with bad parameters
        if ($offset < 0)
        {
            return $this->error(['message' => 'offset < 0']);
        }
        if ($length < 1 || $length > 200)
        {
            return $this->error(['message' => 'length not in 1..200']);
        }

        $em = $this->getDoctrine()->getManager();
        $this->incMetric($em, 'row40');

        if ($this->isBlockedByIP($em, $r->getClientIp(), self::_GET_TODOS_BLOCK, 'row39'))
        {
            return $this->antispam();
        }

        $r = $this->getDoctrine()->getRepository(Todos::class);
        if ($all == 1)
        {
            $todos = $r->findNext($offset, $length);
        }
        else
        {
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

    public function tryChangeTodo(Request $r, int $id)
    {
        $this->parseJsonBody($r);

        // nothing to change
        if (!$r->request->has('completed'))
        {
            return $this->ok();
        }

        $completed = $r->request->getBoolean('completed');

        $em = $this->getDoctrine()->getManager();
        $this->incMetric($em, 'row80');

        if ($this->isBlockedByIP($em, $r->getClientIp(), self::_TRY_CHANGE_TODO_BLOCK, 'row78'))
        {
            return $this->antispam();
        }

        $todo = $em->getRepository(Todos::class)->find($id);

        // unknown id, nothing to change
        if (!isset($todo))
        {
            $this->incMetric($em, 'row79');
            return $this->error(['message' => 'unknown id, nothing to change']);
        }

        $todo->setCompleted($completed);
        $em->flush();
        return $this->ok();
    }
}
