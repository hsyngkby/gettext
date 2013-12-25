<?php

use Hsyngkby\gettext\Commands\ExtractCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class ExtractCommandTest extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders ()
    {
        return array(
            'Hsyngkby\gettext\gettextServiceProvider',
        );

    }

    protected function getPackageAliases ()
    {
        return array(
            'gettext' => 'Hsyngkby\gettext\Facades\gettext',
        );

    }

    public function testExtractCommandSuccessfull ()
    {
        File::shouldReceive('glob')->times(2)->andReturn(array("test.php"));

        $proc = m::mock("Symfony\Component\Process\Process");
        $procBuilder = m::mock("Symfony\Component\Process\ProcessBuilder");

        $proc->shouldReceive('run')->once();
        $proc->shouldReceive('isSuccessful')->once()->andReturn(true);
        $proc->shouldReceive('stop')->once();

        $procBuilder->shouldReceive('setArguments')->once()->andReturn(m::self());
        $procBuilder->shouldReceive('getProcess')->once()->andReturn($proc);

        $commandTester = new CommandTester(new ExtractCommand($procBuilder));
        $proc->__destruct(); // invoke the stop() call
        $commandTester->execute(array());

        $this->assertStringEndsWith("xgettext successfully executed\n", $commandTester->getDisplay());

    }

    public function testExtractCommandThrowsExceptionWhenNoFilesFound ()
    {
        $this->setExpectedException('Hsyngkby\gettext\NoFilesToExtractFromException');
        File::shouldReceive('glob')->times(2)->andReturn(array());

        $commandTester = new CommandTester(new ExtractCommand);
        $commandTester->execute(array());

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>