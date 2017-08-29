<?php

namespace Tests\Feature;

use App\User;
use App\Client;
use Carbon\Carbon;
use Tests\TestCase;
use App\Events\NewBuild;
use App\Events\ClientShouldScan;
use App\Events\ClientWasUpgraded;
use App\Events\ClientShouldUpgrade;
use App\Events\ClientShouldSendHeartbeat;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ClientTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_client_with_a_valid_name_and_version_can_register()
    {
        // act
        $this->post("/api/v1/clients/test-computer-name", [
            'version' => '1.0.1.0'
        ])

        // database assertions
        ->assertDatabaseHas('clients', [
            'name' => 'test-computer-name',
            'version' => '1.0.1.0'
        ])

        // response assertions
        ->response()
            ->assertStatus(201)
            ->assertJsonFragment(['name' => 'test-computer-name'])
            ->assertJsonFragment(['version' => '1.0.1.0']);
    }

    /** @test */
    function an_existing_client_can_register_an_update()
    {
        factory(Client::class)->create([
            'name' => 'Test-Computer-Name',
            'version' => '1.0.0.0'
        ]);

        // act
        $this->post("/api/v1/clients/test-computer-name", [
            'version' => '1.0.1.0'
        ])

        // database assertions
        ->assertDatabaseHas('clients', [
            'name' => 'test-computer-name',
            'version' => '1.0.1.0'
        ])

        // response assertions
        ->response()
            ->assertStatus(202)
            ->assertJsonFragment(['name' => 'test-computer-name'])
            ->assertJsonFragment(['version' => '1.0.1.0']);

        $this->assertEquals( 1, Client::count() );
    }

    /** @test */
    function an_existing_client_can_send_its_os_name()
    {
        factory(Client::class)->create([
            'name' => 'Test-Computer-Name',
            'version' => '1.0.0.0'
        ]);

        // act
        $this->post("/api/v1/clients/test-computer-name", [
            'version' => '1.0.1.0',
            'os' => 'Windows 10'
        ])

        // database assertions
        ->assertDatabaseHas('clients', [
            'name' => 'test-computer-name',
            'version' => '1.0.1.0',
            'os' => 'Windows 10'
        ])

        // response assertions
        ->response()
            ->assertStatus(202)
            ->assertJsonFragment(['name' => 'test-computer-name'])
            ->assertJsonFragment(['version' => '1.0.1.0'])
            ->assertJsonFragment(['os' => 'Windows 10']);

        $this->assertEquals( 1, Client::count() );
    }

    /** @test */
    function a_client_with_a_null_name_cannot_register()
    {
        $this->disableExceptionHandling();

        try {
            $clientName = null;

            // act
            $this->post("/api/v1/clients/{$clientName}", [
                'version' => '1.0.1.0'
            ]);
        }

        catch( MethodNotAllowedHttpException $e)
        {
            $this->assertDatabaseMissing('clients', ['version' => '1.0.1.0']);
            return;
        }

        $this->fail("Expected a route not found exception, but did not get one.");
    }

    /** @test */
    function a_clients_details_can_be_retrieved()
    {
        // set up
        factory(Client::class)->create(['name' => 'test-computer-name']);
        $this->assertDatabaseHas('clients', ['name' => 'test-computer-name']);

        // act
        $this->get("/api/v1/clients/test-computer-name")

        // response assertions
        ->response()
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'test-computer-name']);
    }

    /** @test */
    function multiple_clients_details_can_be_retrieved()
    {
        // set up
        $client1 = factory(Client::class)->create();
        $client2 = factory(Client::class)->create();

        // act
        $this->get("/api/v1/clients/")

        // response assertions
        ->response()
            ->assertStatus(200)
            ->assertJsonFragment(['name' => $client1->name])
            ->assertJsonFragment(['name' => $client2->name]);
    }

    /** @test */
    function a_client_heartbeat_advances_the_heartbeat_timestamp_of_the_client()
    {
        // setup
        $client = factory(Client::class)->create([
            'heartbeat_at' => Carbon::now()->subDay()
        ]);

        $original_timestamp = $client->heartbeat_at;

        // act
        $this->get("/api/v1/clients/{$client->name}/heartbeat")

        // response assertions
        ->response()
            ->assertStatus(202);

        $this->assertTrue( $client->fresh()->heartbeat_at->gt($original_timestamp) );
    }

    /** @test */
    function a_client_can_be_instructed_to_request_a_heartbeat()
    {
        $this->disableExceptionHandling();

        $client = factory(Client::class)->create();

        // act
        $this->get("/api/v1/clients/{$client->name}/marco")
            ->response()
            ->assertStatus(202);

        $this->assertEvent(ClientShouldSendHeartbeat::class, [ 'client' => $client ]);
    }

    /** @test */
    function a_client_can_be_updated_to_a_new_version()
    {
        // setup
        $client = factory(Client::class)->create([
            'version' => 'original_version'
        ]);

        $this->assertDatabaseHas('clients', [
            'version' => 'original_version'
        ])

        // act
        ->patch("/api/v1/clients/{$client->name}", [
            'version' => 'new_version'
        ])

        // database assertions
        ->assertDatabaseHas('clients', [
            'version' => 'new_version'
        ])

        // response assertions
        ->response()
            ->assertStatus(202);
    }

    /** @test */
    function a_client_can_be_told_to_upgrade_by_an_admin_via_an_event()
    {
        $client = factory(Client::class)->create();

        $this
        ->actingAsAdmin()
        ->post("/api/v1/clients/{$client->name}/upgrade");

        $this->assertEvent(ClientShouldUpgrade::class, ['client' => $client]);
    }

    /** @test */
    function multiple_clients_can_be_told_to_upgrade_by_an_admin_via_an_event()
    {
        $this->disableExceptionHandling();

        $client1 = factory(Client::class)->create();
        $client2 = factory(Client::class)->create();

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/_upgrade", [
                "clients" => [ $client1->id, $client2->id ]
            ]);

        $this->assertEvent(ClientShouldUpgrade::class, ['client' => $client1]);
        $this->assertEvent(ClientShouldUpgrade::class, ['client' => $client2]);
    }

    /** @test */
    function multiple_clients_can_be_told_to_scan_by_an_admin_via_an_event()
    {
        $this->disableExceptionHandling();

        $client1 = factory(Client::class)->create();
        $client2 = factory(Client::class)->create();

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/_scan", [
                "clients" => [ $client1->id, $client2->id ]
            ]);

        $this->assertEvent(ClientShouldScan::class, ['client' => $client1]);
        $this->assertEvent(ClientShouldScan::class, ['client' => $client2]);
    }

    /** @test */
    function an_event_is_fired_after_a_client_is_upgraded()
    {
        $this->disableExceptionHandling();

        $client = factory(Client::class)->create(['version' => '1.0.0']);

        $this->assertDatabaseHas('clients', [
            'version' => '1.0.0'
        ])

        // act
        ->patch("/api/v1/clients/{$client->name}", [
            'version' => '1.0.1'
        ]);

        $this->assertEvent(ClientWasUpgraded::class, ['client' => $client]);
    }

    /** @test */
    function clients_can_be_told_of_a_new_build_via_event()
    {
        $this->disableExceptionHandling();

        $this
        ->actingAsAdmin()
        ->post("/api/v1/builds")


        ->response()
            ->assertStatus(202);

        $this->assertEvent(NewBuild::class);
    }

    /** @test */
    function a_client_can_be_told_to_scan_via_event()
    {
        $client = factory(Client::class)->create();

        $this->disableExceptionHandling();

        $this
        ->actingAsAdmin()
        ->post("/api/v1/clients/{$client->name}/scan")


        ->response()
            ->assertStatus(202);

        $this->assertEvent(ClientShouldScan::class, ['client' => $client]);
    }

    /** @test */
    function a_client_can_be_deleted_by_an_admin()
    {
        $client = factory(Client::class)->create();

        $this
            ->actingAsAdmin()
            ->delete("/api/v1/clients/{$client->name}")

        ->response()
            ->assertStatus(202);

        $this->assertFalse( $client->exists() );
    }

    /** @test */
     function clients_can_be_mass_deleted_by_an_admin()
     {
        $this->disableExceptionHandling();

         $clients = factory(Client::class, 5)->create();

         $this->actingAsAdmin()
            ->post("/api/v1/clients/_delete", [
                'clients' => $clients->pluck('id')
            ])

        ->response()
            ->assertStatus(202);

        $this->assertEquals(0, Client::count());
     }

    /** @test */
    function a_clients_scanned_file_count_can_be_updated()
    {
        $client = factory(Client::class)->create();

        $this
            ->post("/api/v1/clients/{$client->name}/count", ['count' => 123456])

        ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('clients',[
            'scanned_files_count' => 123456
        ]);
    }

    /** @test */
    function a_clients_current_scanned_file_count_can_be_updated()
    {
        $client = factory(Client::class)->create();

        $this
            ->post("/api/v1/clients/{$client->name}/count_current", ['count' => 1234])

        ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('clients',[
            'scanned_files_current' => 1234
        ]);
    }

    /** @test */
    function the_latest_agent_build_can_be_obtained()
    {
        // given some clients
        factory(Client::class)->create(['version' => '1.0.1.0']);
        factory(Client::class)->create(['version' => '1.0.2.0']);
        factory(Client::class)->create(['version' => '1.0.3.0']);

        $this->get('/api/v1/agent-build')

        ->response()
            ->assertStatus(200)
            ->assertJsonFragment(['version' => '1.0.3.0']);
    }

}
