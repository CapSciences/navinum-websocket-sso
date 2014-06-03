<?php

namespace CapSciences\ServerVip\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testListapp()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/list/app');
    }

    public function testAddapp()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/add/app');
    }

}
