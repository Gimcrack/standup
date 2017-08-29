<?php

namespace Tests\Feature;

use Definitions;
use App\Exemption;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DefinitionsTest extends TestCase {
    use DatabaseMigrations;

    /** @test */
    function the_current_definitions_can_be_retrieved()
    {   
        $this->disableExceptionHandling();
        
        // setup
        Definitions::fake();

        // act
        $this->get('api/v1/definitions')

        // assert
        ->response()
            ->assertStatus(200)
            ->assertJsonFragment(['foo'])
            ->assertJsonFragment(['bar'])
            ->assertJsonFragment(['biz']);
    }

    /** @test */
    function definitions_that_match_exemptions_are_not_retrieved()
    {
        $this->disableExceptionHandling();

        factory(Exemption::class)->create(['pattern' => 'foo']);

        Definitions::fake();

        // act
        $this->get('api/v1/definitions')

        // assert
        ->response()
            ->assertStatus(200)
            ->assertJsonMissing(['foo']);

    }

    /** @test */
    function the_definitions_status_can_be_retrieved()
    {
        $this->get('api/v1/definitions-status')

        ->response()
            ->assertStatus(200)
            ->assertJsonFragment(['definitions']);

    }


}