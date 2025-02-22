<?php

namespace Tests\Feature;

use Tests\TestCase;

class PostTest extends TestCase
{

    public function testIndex()
    {
        $response = $this->get('/api/posts');
        $response->assertStatus(200);

        $responseData = $response->json();
        $this->assertNotNull($responseData, 'data is null');
        $this->assertArrayHasKey('posts', $responseData, 'posts key 누락');
    }
}
