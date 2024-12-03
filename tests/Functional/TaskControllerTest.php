<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Status;

class TaskControllerTest extends WebTestCase
{
    public function testListTasks(): void
    {
        $client = static::createClient();
        $client->request('GET', '');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateTask(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tasks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'New Task',
            'description' => 'Description of the new task',
            'status' => Status::TODO,
        ]));

        print(json_encode([
            'title' => 'New Task',
            'description' => 'Description of the new task',
            'status' => Status::TODO,
        ]));

        $this->assertResponseStatusCodeSame(201);
    }
}