<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportBooksCommandTest extends KernelTestCase
{
    public function testExecuteWithNonExistentFile(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:import:books');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['file' => '/non/existent/file.csv']);

        self::assertStringContainsString('File not found', $commandTester->getDisplay());
        self::assertEquals(1, $commandTester->getStatusCode());
    }
}
