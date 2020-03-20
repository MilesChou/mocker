<?php

namespace Tests\Unit;

use MilesChou\HttpClientHelper\Sample;
use Tests\TestCase;

class SampleTest extends TestCase
{
    /**
     * @test
     */
    public function sample(): void
    {
        $this->assertTrue((new Sample())->alwaysTrue());
    }
}
