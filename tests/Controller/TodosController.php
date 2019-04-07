<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TodosControllerTest extends WebTestCase
{
	public function testGetTodos()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);
		$client->request('GET', '/todo');

		// is ok
		$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

		// test response
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('ok', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
		$this->assertCount(2, $response['response']);
		$this->assertArrayHasKey('todos', $response['response']);
		$this->assertTrue(is_array($response['response']['todos']));
		$this->assertArrayHasKey('has_next', $response['response']);
		$this->assertTrue(is_bool($response['response']['has_next']));
	}

	public function testGetTodosLengthLt1()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);
		$client->request('GET', '/todo?length=0');

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

		// test response
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}

	public function testGetTodosLengthGt200()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);
		$client->request('GET', '/todo?length=201');

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

		// test response
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}

	public function testGetTodosOffsetLt0()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);
		$client->request('GET', '/todo?offset=-1');

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

		// test response
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}

	public function testGetTodosBlock()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);

		// create first 20 requests
		for ($i = 0; $i < 20; $i++)
		{
			$client->request('GET', '/todo');

			// all of them should be successful
			$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
		}

		// the last one should be blocked
		$client->request('GET', '/todo');
		$this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $client->getResponse()->getStatusCode());
	}

	public function testTryChangeTodo()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);
		$headers = [
			'CONTENT_TYPE' => 'application/json',
		];
		$body = json_encode([
			'completed' => true,
		]);
		$client->request('PATCH', '/todo/1', [], [], $headers, $body);

		// is ok
		$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

		// test response
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('ok', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
		$this->assertCount(0, $response['response']);
	}

	public function tryChangeTodoUknownId()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);
		$headers = [
			'CONTENT_TYPE' => 'application/json',
		];
		$body = json_encode([
			'completed' => true,
		]);
		$client->request('PATCH', '/todo/10000', [], [], $headers, $body);

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

		// test response
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}

	public function testTryChangeTodoBlock()
	{
		$client = static::createClient([], ['REMOTE_ADDR' => $this->getRandomIP()]);
		$headers = [
			'CONTENT_TYPE' => 'application/json',
		];
		$body = json_encode([
			'completed' => true,
		]);

		// create first 20 requests
		for ($i = 0; $i < 20; $i++)
		{
			$client->request('PATCH', '/todo/1', [], [], $headers, $body);

			// all of them should be successful
			$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
		}

		// the last one should be blocked
		$client->request('PATCH', '/todo/1', [], [], $headers, $body);
		$this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $client->getResponse()->getStatusCode());
	}

	// -------------------------------------------------------
	// Util Methods
	// -------------------------------------------------------

	private function getRandomIP(): string
	{
		return '255.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
	}
}
