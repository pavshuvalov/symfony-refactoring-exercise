<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TodosControllerTest extends WebTestCase
{
	public function testGetTodos()
	{
		$c = static::createClient();
		$c->request('GET', '/todo');

		// is ok
		$this->assertEquals(Response::HTTP_OK, $c->getResponse()->getStatusCode());

		// test response
		$response = json_decode($c->getResponse()->getContent(), true);
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
		$c = static::createClient();
		$c->request('GET', '/todo?length=0');

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $c->getResponse()->getStatusCode());

		// test response
		$response = json_decode($c->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}

	public function testGetTodosLengthGt200()
	{
		$c = static::createClient();
		$c->request('GET', '/todo?length=201');

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $c->getResponse()->getStatusCode());

		// test response
		$response = json_decode($c->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}

	public function testGetTodosOffsetLt0()
	{
		$c = static::createClient();
		$c->request('GET', '/todo?offset=-1');

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $c->getResponse()->getStatusCode());

		// test response
		$response = json_decode($c->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}

	public function testTryChangeTodo()
	{
		$c = static::createClient();
		$h = [
			'CONTENT_TYPE' => 'application/json',
		];
		$body = json_encode([
			'completed' => true,
		]);
		$c->request('PATCH', '/todo/1', [], [], $h, $body);

		// is ok
		$this->assertEquals(Response::HTTP_OK, $c->getResponse()->getStatusCode());

		// test response
		$response = json_decode($c->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('ok', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
		$this->assertCount(0, $response['response']);
	}

	public function tryChangeTodoUknownId()
	{
		$c = static::createClient();
		$h = [
			'CONTENT_TYPE' => 'application/json',
		];
		$body = json_encode([
			'completed' => true,
		]);
		$c->request('PATCH', '/todo/10000', [], [], $h, $body);

		// is bad request
		$this->assertEquals(Response::HTTP_BAD_REQUEST, $c->getResponse()->getStatusCode());

		// test response
		$response = json_decode($c->getResponse()->getContent(), true);
		$this->assertCount(2, $response);
		$this->assertArrayHasKey('status', $response);
		$this->assertEquals('error', $response['status']);
		$this->assertArrayHasKey('response', $response);
		$this->assertTrue(is_array($response['response']));
	}
}
