<?php

class HelpersTest extends PHPUnit_Framework_TestCase {

    public function test_generate_slug()
    {
        $actual = generate_slug(['title' => 'Hello John']);
        $expected = time().'-hello-john';
        $this->assertEquals($expected, $actual);
    }
}