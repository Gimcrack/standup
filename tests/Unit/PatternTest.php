<?php

namespace Tests\Unit;

use App\Client;
use App\Pattern;
use Tests\TestCase;
use App\Events\PatternWasCreated;
use App\Events\PatternWasUpdated;
use App\Events\PatternWasDestroyed;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PatternTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_pattern_must_have_a_name()
    {
        try {
            factory(Pattern::class)->create(['name' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, Pattern::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_pattern_must_have_a_unique_name()
    {
        // given a pattern with a given name
        factory(Pattern::class)->create(['name' => 'pattern_name']);

        try { // create another pattern with the same name
            factory(Pattern::class)->create(['name' => 'pattern_name']);                   
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount( 1, Pattern::where('name','pattern_name')->get() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_pattern_is_published_by_default()
    {
        // given a pattern
        $pattern = factory(Pattern::class)->create();

        $this->assertTrue( !! $pattern->fresh()->published_flag );
    }

    /** @test */
    function a_published_pattern_can_be_unpublished()
    {
        // given a published pattern
        $pattern = factory(Pattern::class)->create([
            'published_flag' => 1
        ]);

        // act - unpublish the pattern
        $pattern->unpublish();

        $this->assertFalse( !! $pattern->fresh()->published_flag );
    }

    /** @test */
    function an_unpublished_pattern_can_be_published()
    {
        // given an unpublished pattern
        $pattern = factory(Pattern::class)->create([
            'published_flag' => 0
        ]);

        // act - publish the pattern
        $pattern->publish();

        $this->assertTrue( !! $pattern->fresh()->published_flag );
    }

    /** @test */
    function an_event_is_dispatched_when_a_pattern_is_created()
    {
        $pattern = factory(Pattern::class)->create();

        $this->assertEvent(PatternWasCreated::class, [ 'pattern' => $pattern ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_pattern_is_updated()
    {   
        // given a published pattern
        $pattern = factory(Pattern::class)->states('published')->create();

        // act - update the pattern
        $pattern->unpublish();

        $this->assertEvent(PatternWasUpdated::class, [ 'pattern' => $pattern ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_pattern_is_destroyed()
    {   
        // given a pattern
        $pattern = factory(Pattern::class)->create();

        // act - update the pattern
        $pattern->delete();

        $this->assertEvent(PatternWasDestroyed::class, [ 'pattern' => $pattern ]);
    }
}
