<?php

namespace Fld3\PassportPgtClient\Feature\Http\Controllers;

use Tests\TestCase;

/**
 * Class AuthControllerTest
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class AuthControllerTest extends TestCase
{
    /**
     * Example Test
     *
     * @test
     */
    public function example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
