<?php

namespace Tests\Unit;

use App\Client;
use App\LogEntry;
use Carbon\Carbon;
use Tests\TestCase;
use App\Events\LogEntryWasCreated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogEntryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_log_entry_must_have_a_client_id()
    {
        try {
            $log_entry = factory(LogEntry::class)->create(['client_id' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, LogEntry::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_log_entry_must_have_an_action()
    {
        try {
            $log_entry = factory(LogEntry::class)->create(['action' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, LogEntry::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_log_entry_must_have_a_status()
    {
        try {
            $log_entry = factory(LogEntry::class)->create(['status' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, LogEntry::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function a_log_entry_has_an_associated_client()
    {
        // given a log entry with a valid client
        $entry = factory(LogEntry::class)->create([
            'client_id' => factory(Client::class)->create()->id
        ]);

        // make sure the log entry has a client
        $this->assertInstanceOf(Client::class,$entry->client);
    }

    /** @test */
    function a_history_of_recent_log_entries_can_be_obtained()
    {
        // given some recent log entries
        factory(LogEntry::class, 10)->create();

        // and some old ones
        factory(LogEntry::class, 10)->create([
            'created_at' => Carbon::now()->subDays(10)
        ]);

        // make sure we only get the recent ones
        $this->assertCount(10, LogEntry::recent($days = 7)->get() );
    }

    /** @test */
    function an_event_is_dispatched_when_a_client_is_created()
    {
        $log_entry = factory(LogEntry::class)->create();

        $this->assertEvent(LogEntryWasCreated::class, [ 'log_entry' => $log_entry ]);
    }
}
