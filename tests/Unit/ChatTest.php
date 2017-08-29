<?php

namespace Tests\Unit;

use App\Chat;
use App\User;
use App\Pattern;
use Tests\TestCase;
use App\MatchedFile;
use App\Events\UserWasCreated;
use App\Events\UserWasUpdated;
use App\Events\UserWasDestroyed;
use App\Events\ChatMessageReceived;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MatchedFileCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChatTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_chat_must_have_a_user()
    {
        $this->disableExceptionHandling();

        try {
            factory(Chat::class)->create(['user_id' => null]);
        }   
        catch( QueryException $e) {
            $this->assertEquals(0, Chat::count());
            return;
        }
        
        $this->fail("Expected a QueryException but did not get one");
    }

    /** @test */
    function a_chat_belongs_to_a_user()
    {
        $user = factory(User::class)->create();
        $chat = factory(Chat::class)->create(['user_id' => $user->id]);

        $this->assertTrue( $chat->user->is($user) );
    }

    /** @test */
    function a_user_can_have_chats()
    {
        $user = factory(User::class)->create();
        $chat = factory(Chat::class)->create(['user_id' => $user->id]);

        $this->assertTrue( $user->chats()->first()->is($chat) );
    }

    /** @test */
    function a_chat_must_have_a_message()
    {
        $this->disableExceptionHandling();

        try {
            factory(Chat::class)->create(['message' => null]);
        }   
        catch( QueryException $e) {
            $this->assertEquals(0, Chat::count());

            return;
        }

        $this->fail("Expected a QueryException but did not get one");
    }

    /** @test */
    function an_event_is_sent_when_a_chat_is_received()
    {
        $chat = factory(Chat::class)->create();

        $this->assertEvent(ChatMessageReceived::class, compact('chat'));
    }
}
