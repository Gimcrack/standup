<?php

namespace Tests\Integration;

use App\Definitions\FakeDefinitions;
use App\Definitions\Facades\Definitions;


class FakeDefinitionsTest extends DefinitionsProviderTests
{
    public function setUp()
    {
        parent::setUp();

        $this->minCount = 3;
        
        $this->implementation = FakeDefinitions::class;

        $this->implementation_short = 'FakeDefinitions';

        Definitions::fake();
    }
}
