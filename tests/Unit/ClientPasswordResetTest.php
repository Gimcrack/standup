<?php

namespace Tests\Unit;

use App\Client;
use App\LogEntry;
use Carbon\Carbon;
use Tests\TestCase;
use App\MatchedFile;
use App\ClientPasswordReset;
use App\Events\ClientWasCreated;
use App\Events\ClientWasUpdated;
use App\Events\ClientWasDestroyed;
use App\Events\ClientShouldSendHeartbeat;
use App\Events\ClientPasswordResetWasCompleted;
use App\Events\ClientPasswordResetWasRequested;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientPasswordResetTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_client_password_reset_must_have_a_client()
    {
        $this->disableExceptionHandling();

        try {
            $reset = factory(ClientPasswordReset::class)->create(['client_id' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, ClientPasswordReset::all() );

            $reset = factory(ClientPasswordReset::class)->create();

            $this->assertTrue( $reset->client->is( Client::first() ) );
            return;
        }
        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_client_password_reset_must_have_a_unique_client()
    {
        $reset = factory(ClientPasswordReset::class)->create(['client_id' => 1]);

        try {
            $reset = factory(ClientPasswordReset::class)->create(['client_id' => 1]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(1, ClientPasswordReset::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_client_password_reset_can_be_obtained_for_a_client()
    {
        $client = factory(Client::class)->create();

        $reset = ClientPasswordReset::forClient($client);

        $this->assertTrue( $reset->is( ClientPasswordReset::first() ) );
    }

    /** @test */
    function a_client_password_reset_can_be_requested()
    {
        $client = factory(Client::class)->create();

        $reset = ClientPasswordReset::forClient($client);

        $this->assertEquals(null, $reset->requested_at);

        $reset->request("NewPassword123#");

        $this->assertTrue( $reset->fresh()->requested_at->gt( Carbon::yesterday() ) );
    } 

    /** @test */
    function a_client_password_reset_can_be_completed()
    {
        $client = factory(Client::class)->create();

        $reset = ClientPasswordReset::forClient($client);

        $this->assertEquals(null, $reset->completed_at);

        $reset->complete();

        $this->assertTrue( $reset->fresh()->completed_at->gt( Carbon::yesterday() ) );
    }    

    /** @test */
    function an_event_is_fired_when_a_reset_is_requested()
    {
        $client = factory(Client::class)->create();
        $password = 'new-password';

        ClientPasswordReset::forClient($client)->request($password);

        $this->assertEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function an_event_is_fired_when_a_reset_is_completed()
    {
        $client = factory(Client::class)->create();

        ClientPasswordReset::forClient($client)->complete();

        $this->assertEvent(ClientPasswordResetWasCompleted::class, compact('client'));
    }

    /** @test */
    function a_client_can_have_a_password_reset_record()
    {
        $client = factory(Client::class)->create();

        $reset = ClientPasswordReset::forClient($client);

        $this->assertTrue( $client->password_reset->is($reset) );
    }

    /** @test */
    function a_client_can_tell_if_its_password_has_been_reset_recently()
    {
        $client = factory(Client::class)->create();

        $this->assertFalse( $client->password_reset_recently );

        ClientPasswordReset::forClient($client)->complete();

        $this->assertTrue( $client->fresh()->password_reset_recently );

    }
}
