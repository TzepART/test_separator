<?php

namespace TestSeparator\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
use TestSeparator\Command\SeparateTestsCommand;
use TestSeparator\Handler\SeparateTestsHandler;

class SeparateTestsCommandTest extends TestCase
{
    /**
     * @return array
     */
    public function dataRun()
    {
        return [
            'separate-tests' => [new SeparateTestsCommand(new SeparateTestsHandler(), 'separate-tests')],
        ];
    }

    /**
     * @dataProvider dataRun()
     *
     * @param Command $command
     */
    public function testRun(Command $command)
    {
        $input  = $this
            ->getMockBuilder(Input::class)
            ->disableOriginalConstructor()
            ->getMock();
        $output = $this
            ->getMockBuilder(Output::class)
            ->disableOriginalConstructor()
            ->setMethods(['write', 'doWrite'])
            ->getMock();
        $output->expects(self::atLeastOnce())
            ->method('write');

        self::assertEquals(
            0,
            $command->run($input, $output)
        );
    }
}
