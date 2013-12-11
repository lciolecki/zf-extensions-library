<?php
namespace ExtlibTest\Validate;

class PeselTest extends \PHPUnit_Framework_TestCase
{
    public function pesels()
    {
        return array(
            [46042919999.0, false],
            [new \stdClass(), false],
            [function(){}, false],
            [array(), false],
            [null, false],
            ["", false],
            ["46042919991", false],
            ["46042919999", true],
            [46042919999, true]
        );
    }

    /**
     * @dataProvider pesels
     * @test
     */
    public function assertPesel($pesel, $expected)
    {
        $validator = new \Extlib\Validate\Pesel();
        $result = $validator->isValid($pesel);
        $this->assertEquals($expected, $result);
    }
}