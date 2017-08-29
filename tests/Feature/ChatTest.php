<?php

namespace Tests\Feature;

use App\User;
use App\Chat;
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

class ChatTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_send_a_chat_message()
    {
        $this->disableExceptionHandling();

        $this->actingAsUser()
            ->post('api/v1/chat',['message' => 'Hello World'])
            ->response()
                ->assertStatus(201);

        $this->assertDatabaseHas('chats',['message' => 'Hello World']);
    }

    /** @test */
    function a_guest_cannot_send_a_chat_message()
    {
        $this
            ->post('api/v1/chat',['message' => 'Hello World'])
            ->response()
                ->assertStatus(401);

        $this->assertDatabaseMissing('chats',['message' => 'Hello World']);
    }

    /** @test */
    function a_listing_of_chat_messages_can_be_retrieved()
    {
        $this->disableExceptionHandling();

        $chats = factory(Chat::class,3)->create();

        $this->get('api/v1/chat')
            ->assertJsonCount(3)
            ->response()
                ->assertStatus(200)
                
                ->assertJsonFragment(['message' => $chats[0]->message])
                ->assertJsonFragment(['message' => $chats[1]->message])
                ->assertJsonFragment(['message' => $chats[2]->message]);

    }
    
}