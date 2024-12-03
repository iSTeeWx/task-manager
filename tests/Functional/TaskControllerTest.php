<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Status;
use Symfony\Component\HttpFoundation\Response;

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

        $res = $client->getResponse();
        $resdata = json_decode($res->getContent(), true);
        $this->assertResponseStatusCodeSame(201);
        $this->assertEquals("Task created successfully", $resdata['message']);
    }

    public function testModifyTask(): void
    {
        $client = static::createClient();

        $client->request('POST', '/tasks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Task to be modified',
            'description' => 'Description of the task',
            'status' => Status::DONE,
        ]));

        $res = $client->getResponse();
        $taskId = json_decode($res->getContent(), true)['id'];

        $client->request('PUT', '/tasks/' . $taskId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Task has been modified',
            'description' => 'New description of the task',
            'status' => Status::IN_PROGRESS,
        ]));

        $this->assertResponseIsSuccessful();

        $res = $client->getResponse();
        $resdata = json_decode($res->getContent(), true);
        $this->assertEquals("Task updated successfully", $resdata['message']);
    }

    public function testDeleteTask(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tasks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Task to be deleted',
            'description' => 'Description of the task',
            'status' => Status::DONE,
        ]));

        $res = $client->getResponse();
        $taskId = json_decode($res->getContent(), true)['id'];

        $client->request('DELETE', '/tasks/' . $taskId);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $client->request('DELETE', '/tasks/' . $taskId);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}