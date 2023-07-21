<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExceptionsHandlerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function json_api_errors_are_only_shown_to_requests_with_the_prefix_api()
    {
        $this->getJson('api/route')
            ->assertJsonApiError(
                detail: 'The route api/route could not be found.'
            );

        $this->getJson('api/v1/invalid-resource/invalid-id')
            ->assertJsonApiError(
                detail: 'The route api/v1/invalid-resource/invalid-id could not be found.'
            );
    }
}
