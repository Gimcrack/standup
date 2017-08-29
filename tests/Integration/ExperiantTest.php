<?php

namespace Tests\Integration;

use Cache;
use Carbon\Carbon;
use App\Definitions\Experiant;
use Illuminate\Support\Collection;
use App\Events\DefinitionsWereUpdated;
use App\Definitions\Facades\Definitions;

/**
 * @group external
 */
class ExperiantTest extends DefinitionsProviderTests
{
    public function setUp()
    {
        parent::setUp();

        $this->minCount = 1000;
        
        $this->implementation = Experiant::class;

        $this->implementation_short = 'Experiant';
    }

    /** @test */
    function an_event_is_not_fired_when_the_definitions_have_not_been_updated_recently()
    {
        Cache::forever('definitionsLastUpdated', Carbon::now()->addDay() );

        Definitions::fetch();

        $this->assertNotEvent(DefinitionsWereUpdated::class);
    }
}
