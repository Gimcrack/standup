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
use App\Events\ClientPasswordResetWasCompleted;
use App\Events\ClientPasswordResetWasRequested;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ClientPasswordResetTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_master_password_is_required_to_request_a_client_password_reset()
    {
        $client = factory(Client::class)->create();
        $password = 'SomeLongValidPassword123!@#';
    
        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/{$client->name}/admin-password-reset", [
                'master_password' => 'incorrect-master-password',
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->response()
                ->assertStatus(422);

        $this->assertNotEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function a_client_password_reset_can_be_requested_by_an_admin()
    {
        $client = factory(Client::class)->create();
        $password = 'SomeLongValidPassword123!@#';

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/{$client->name}/admin-password-reset", [
                'master_password' => 'test-master-password',
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->response()
                ->assertStatus(202);

        $this->assertEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function a_client_password_reset_must_have_a_password()
    {
        $client = factory(Client::class)->create();
        $password = '';

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/{$client->name}/admin-password-reset", [
                'master_password' => 'test-master-password',
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->response()
                ->assertStatus(422);

        $this->assertNotEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function a_client_password_reset_must_have_a_confirmed_password()
    {
        $client = factory(Client::class)->create();
        $password = 'SomeLongContrivedPassword123!@#';

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/{$client->name}/admin-password-reset", [
                'master_password' => 'test-master-password',
                'password' => $password,
                'password_confirmation' => 'not-a-matching-password'
            ])
            ->response()
                ->assertStatus(422);

        $this->assertNotEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function a_client_password_reset_must_have_a_password_meeting_the_min_length()
    {
        $client = factory(Client::class)->create();
        $password = 'Some-short-p@ssword';

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/{$client->name}/admin-password-reset", [
                'master_password' => 'test-master-password',
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->response()
                ->assertStatus(422);

        $this->assertNotEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function a_client_password_reset_must_have_a_password_meeting_the_complexity_requirements()
    {
        $client = factory(Client::class)->create();
        $password = 'some-super-long-incomplex-password';

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/{$client->name}/admin-password-reset", [
                'master_password' => 'test-master-password',
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->response()
                ->assertStatus(422);

        $this->assertNotEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function many_client_password_resets_may_be_requested_at_the_same_time()
    {
        $this->disableExceptionHandling();

        $clients = factory(Client::class,2)->create();
        $password = 'SomeLongValidPassword123!@#';

        // dd($clients->toArray());

        $this
            ->actingAsAdmin()
            ->post("api/v1/admin-password-reset", [
                'master_password' => 'test-master-password',
                'clients' => $clients->pluck('name'),
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->response()
                ->assertStatus(202);

        $client = $clients[0];        
        $this->assertEvent(ClientPasswordResetWasRequested::class, compact('client','password'));  

        $client = $clients[1];  
        $this->assertEvent(ClientPasswordResetWasRequested::class, compact('client','password'));
    }

    /** @test */
    function a_client_can_complete_a_password_reset_request()
    {
        $this->disableExceptionHandling();
        $client = factory(Client::class)->create();

        $this
            ->actingAsAdmin()
            ->post("api/v1/clients/{$client->name}/admin-password-reset-complete")
            ->response()
                ->assertStatus(202);

        $this->assertEvent(ClientPasswordResetWasCompleted::class, compact('client'));
    }
    
}