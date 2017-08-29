<?php

namespace Tests\Unit;

use App\User;
use App\Pattern;
use Tests\TestCase;
use App\MatchedFile;
use App\Events\UserWasCreated;
use App\Events\UserWasUpdated;
use App\Events\UserWasDestroyed;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MatchedFileCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_be_configured_as_an_admin()
    {
        $user = factory(User::class)->states('admin')->create();

        $this->assertTrue( $user->isAdmin() );
    }

    /** @test */
    function a_user_can_be_promoted_to_an_admin()
    {
        $user = factory(User::class)->create();

        $this->assertFalse( $user->fresh()->isAdmin() );

        $user->promoteToAdmin();

        $this->assertTrue( $user->fresh()->isAdmin() );   
    }

    /** @test */
    function an_event_is_dispatched_when_a_user_is_created()
    {
        $user = factory(User::class)->create();

        $this->assertEvent(UserWasCreated::class, [ 'user' => $user ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_user_is_updated()
    {   
        // given a published user
        $user = factory(User::class)->create();

        // act - update the user
        $user->promoteToAdmin();

        $this->assertEvent(UserWasUpdated::class, [ 'user' => $user ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_user_is_destroyed()
    {   
        // given a user
        $user = factory(User::class)->create();

        // act - update the user
        $user->delete();

        $this->assertEvent(UserWasDestroyed::class, [ 'user' => $user ]);
    }
}
