<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookApplicationTest extends WebTestCase
{

    public function testFilterBook($filter = '')
    {
        $client = static::createClient();

        $response = $client->request('GET', '/?filter=' . $filter . 'Book 1');

//        $books =

        $this->assertResponseIsSuccessful();

    }

}
