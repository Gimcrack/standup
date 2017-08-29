<?php

namespace Tests\Unit;

use App\Pattern;
use App\LogEntry;
use App\Exemption;
use Tests\TestCase;
use App\MatchedFile;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExemptionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function an_exemption_must_have_a_pattern()
    {
        try {
            $client = factory(Exemption::class)->create(['pattern' => null]);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $this->assertCount(0, Exemption::all() );
            return;
        }

        $this->fail("Expected a query exception, but did not get one.");
    }

    /** @test */
    function an_exemption_can_be_created_with_a_valid_pattern()
    {
        factory(Exemption::class)->create([
            'pattern' => 'some_pattern'
        ]);

        $this->assertDatabaseHas('exemptions', [
            'pattern' => 'some_pattern'
        ]);
    }

    /** @test */
    function an_exemption_must_have_a_unique_pattern()
    {
        // given an exemption
        factory(Exemption::class)->create([
            'pattern' => 'some_pattern'
        ]);

        try { // to create another with the same pattern
            factory(Exemption::class)->create([
                'pattern' => 'some_pattern'
            ]);
        }

        catch (QueryException $e) {
            $this->assertCount(1, Exemption::all());
            return;
        }

        $this->fail("Expected a query exception, but did not get one");
    }

    /** @test */
    function an_exemption_can_be_created_from_a_pattern()
    {
        $ex = Exemption::createFromPattern("pattern");
        $ex2 = Exemption::createFromPattern("pattern");

        $this->assertEquals(1,Exemption::count());
        $this->assertEquals('pattern',$ex->pattern);
    }
}
