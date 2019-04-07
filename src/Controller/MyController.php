<?php

namespace App\Controller;

use App\Repository\TodosRepository;
use App\Repository\MetricRepository;
use App\Repository\AntispamIpRepository;
use App\Entity\Service\AntispamIp;
use App\Entity\Stat\Metric;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyController extends AbstractController
{
	function __construct(TodosRepository $todosRepository, MetricRepository $metricRepository, AntispamIpRepository $antispamIpRepository)
	{
		$this->todosRepository = $todosRepository;
		$this->metricRepository = $metricRepository;
		$this->antispamIpRepository = $antispamIpRepository;
	}

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

	protected function incMetric(string $row)
	{
		$entity = $this->metricRepository->find($row);
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
		}

		// save object
		$em = $this->getDoctrine()->getManager('stat');
		$em->persist($entity);
		$em->flush();
    }

	protected function isBlockedByIP(string $ip, array $block, string $stat_row = null): bool
	{
		$em = $this->getDoctrine()->getManager('service');
		$entity = $this->antispamIpRepository->find(['ipv4' => $ip, 'key' => $block['key']]);

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
				$this->incMetric($stat_row);
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
