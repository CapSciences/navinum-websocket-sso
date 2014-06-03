<?php

namespace CapSciences\ServerVip\LayoutBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LanguageControllerControllerTest extends WebTestCase
{
    public function testChange()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/change');
    }

}
