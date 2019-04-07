<?php

namespace App\Controller;

use App\Entity\Metric;
use App\Entity\AntispamIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

class MyController extends AbstractController
{
	/*
		If content-type is application/json and the request body is a valid JSON,
		make $request->request contain parsed request body
	*/
	protected function parseJsonBody(Request $request):void
	{
		$ct = $request->headers->get('content-type');
		if ($ct == 'application/json') {
			$parsed_json_body = json_decode($request->getContent(), true);

			// is valid JSON
			if (isset($parsed_json_body)) {
				$request->request->replace($parsed_json_body);
			}
		}
	}

	// success response shortcut
	protected function ok(array $response = []):JsonResponse
	{
		$r = new JsonResponse([
			'status' => 'ok',
			'response' => count($response) < 1 ? (object) [] : $response,
		]);
		$r->headers->set('content-type', 'application/json');
		return $r;
	}

	// error response shortcut
	protected function error(array $response = []):JsonResponse
	{
		$r = new JsonResponse([
			'status' => 'error',
			'response' => count($response) < 1 ? (object) [] : $response,
		]);
		$r->headers->set('content-type', 'application/json');
		$r->setStatusCode(Response::HTTP_BAD_REQUEST);
		return $r;
	}

	// antispam error response shortcut
	protected function antispam(array $response = []):JsonResponse
	{
		$r = new JsonResponse([
			'status' => 'error',
			'response' => count($response) < 1 ? (object) [] : $response,
		]);
		$r->headers->set('content-type', 'application/json');
		$r->setStatusCode(Response::HTTP_TOO_MANY_REQUESTS);
		return $r;
	}

	protected function incMetric(EntityManager $em, string $row)
	{
		$r = $em->getRepository(Metric::class);
		$entity = $r->find($row);
		if (isset($entity))
		{
			$entity->incValue();
		}
		else
		{
			$entity = new Metric
			(
				$row, // key
				1,    // value
				[]    // extra
			);
			$em->persist($entity);
		}
		$em->flush();
    }

	protected function isBlockedByIP(EntityManager $em, string $ip, array $block, string $stat_row = null): bool
	{
		$r = $em->getRepository(AntispamIp::class);
		$entity = $r->find(['ipv4' => $ip, 'key' => $block['key']]);

		// if antispam entry is not created yet, create it with default values
		if (!isset($entity))
		{
			$entity = new AntispamIp
			(
				$ip,
				$block['key'],
				time() + $block['expire'], // expire
				0,                         // count
				false,                     // is_stat_sent
				[]                         // extra
			);
		}

		// if time is over, reset the block
		if (time() > $entity->getExpire())
		{
			$entity->setExpire(time() + $block['expire']);
			$entity->setCount(0);
			$entity->setIsStatSent(false);
		}

		// if limit is over, user need to be blocked
		if ($entity->getCount() >= $block['count'])
		{
			// collect stat if needed (only 1 time per block!)
			if (isset($stat_row) && !$entity->getIsStatSent())
			{
				$this->incMetric($em, $stat_row);
				$entity->setIsStatSent(true);

				// update antispam data
				$em->persist($entity);
				$em->flush();
			}

			return true;
		}

		$entity->incCount();

		// update antispam data
		$em->persist($entity);
		$em->flush();

		// used does not to be blocked yet
		return false;
	}
}
