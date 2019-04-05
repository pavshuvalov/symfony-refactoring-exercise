<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MyController extends AbstractController
{
	// dependency injection through constructor 
	private $request;

	function __construct()
	{
		/*
			If content-type is application/json and the request body is a valid JSON,
			make $request->request contain parsed request body
		*/
		$this->request = Request::createFromGlobals();
		$ct = $this->request->headers->get('content-type');
		if ($ct == 'application/json') {
			$parsed_json_body = json_decode($this->request->getContent(), true);

			// is valid JSON
			if (isset($parsed_json_body)) {
				$this->request->request->replace($parsed_json_body);
			}
		}
	}

	protected function getRequest():Request
	{
		return $this->request;
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
		return new JsonResponse([
			'status' => 'error',
			'response' => count($response) < 1 ? (object) [] : $response,
		]);
	}
}
