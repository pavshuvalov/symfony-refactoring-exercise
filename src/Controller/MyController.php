<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

	protected function ok(array $response = []):JsonResponse
	{
		return new JsonResponse([
			'status' => 'ok',
			'response' => count($response) < 1 ? (object) [] : $response,
		]);
	}

	protected function error(array $response = []):JsonResponse
	{
		$r = new JsonResponse([
			'status' => 'error',
			'response' => count($response) < 1 ? (object) [] : $response,
		]);
		$r->setStatusCode(Response::HTTP_BAD_REQUEST);
		return $r;
	}
}
