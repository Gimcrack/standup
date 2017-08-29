<?php

namespace Tests\Unit;

use App\Client;
use App\LogEntry;
use Carbon\Carbon;
use Tests\TestCase;
use App\MatchedFile;
use App\Events\ClientWasCreated;
use App\Events\ClientWasUpdated;
use App\Events\ClientWasDestroyed;
use App\Events\ClientSentHeartbeat;
use App\Events\ClientShouldSendHeartbeat;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_client_must_have_a_name()
    {
        $this->disableExceptionHandling();

        try {
            $client = factory(Client::class)->create(['name' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, Client::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_client_must_have_a_unique_name()
    { 
        // given a client
        $client = factory(Client::class)->create(['name' => 'some-name']);

        // act - try to create another client with the same name
        try {
            factory(Client::class)->create(['name' => 'some-name']);            
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(1, Client::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_client_must_have_a_version()
    {
        try {
            $client = factory(Client::class)->create(['version' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, Client::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_client_may_have_associated_logs()
    {
        // given a client
        $client = factory(Client::class)->create();
        
        // and some logs associated with the client
        factory(LogEntry::class)->create(['client_id' => $client->id]);
        factory(LogEntry::class)->create(['client_id' => $client->id]);
        factory(LogEntry::class)->create(['client_id' => $client->id]);

        $this->assertCount( 3, $client->logs );
        
    }

    /** @test */
    function a_client_may_have_associated_matched_files()
    {
        // given a client
        $client = factory(Client::class)->create();
        
        // and some logs associated with the client
        factory(MatchedFile::class)->create(['client_id' => $client->id]);
        factory(MatchedFile::class)->create(['client_id' => $client->id]);
        factory(MatchedFile::class)->create(['client_id' => $client->id]);

        $this->assertCount( 3, $client->matched_files );
        
    }

    /** @test */
    function an_event_is_dispatched_when_a_client_is_created()
    {
        $client = factory(Client::class)->create();

        $this->assertEvent(ClientWasCreated::class, [ 'client' => $client ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_client_is_updated()
    {   
        // given a published client
        $client = factory(Client::class)->create();

        // act - update the client
        $client->update([
            'version' => 'new_version'
        ]);

        $this->assertEvent(ClientWasUpdated::class, [ 'client' => $client ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_client_is_destroyed()
    {   
        // given a client
        $client = factory(Client::class)->create();

        // act - update the client
        $client->delete();

        $this->assertEvent(ClientWasDestroyed::class, [ 'client' => $client ]);
    }

    /** @test */
    function a_client_can_keep_track_of_scanned_file_count()
    {
         // given a client
        $client = factory(Client::class)->create(); 

        // act on the client
        $client->scannedCount(123456);

        $this->assertDatabaseHas('clients',[
            'scanned_files_count' => 123456
        ]);
    }

    /** @test */
    function a_client_can_keep_track_of_current_scanned_file_count()
    {
         // given a client
        $client = factory(Client::class)->create(); 

        // act on the client
        $client->scannedCurrent(1234);

        $this->assertDatabaseHas('clients',[
            'scanned_files_current' => 1234
        ]);
    }

    /** @test */
    function a_client_heartbeat_advances_the_heartbeat_timestamp()
    {
        // given a client
        $client = factory(Client::class)->create([
            'heartbeat_at' => Carbon::parse("1900-01-01")
        ]); 

        // act
        $client->heartbeat();

        $this->assertTrue( $client->fresh()->heartbeat_at->gt( Carbon::yesterday() ) );

        $this->assertEvent( ClientSentHeartbeat::class, [ 'client' => $client ]);
        $this->assertNotEvent( ClientWasUpdated::class, [ 'client' => $client ]);
    }

    /** @test */
    function a_client_can_be_instructed_to_send_a_request_for_a_heartbeat()
    {
        // given a client
        $client = factory(Client::class)->create();

        // act
        $client->requestHeartbeat();

        $this->assertEvent(ClientShouldSendHeartbeat::class, [ 'client' => $client ]);
    }
}
