<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportBooksCommandTest extends KernelTestCase
{
    public function testExecuteWithValidCsvFile(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:import:books');
        $commandTester = new CommandTester($command);

        $csv = tempnam(sys_get_temp_dir(), 'books_');
        if (is_string($csv)) {
            file_put_contents($csv, "title,author,year,isbn\n");
            file_put_contents($csv, "Test Book,Test Author,2024,9780201633610\n", FILE_APPEND);

            $commandTester->execute(['file' => $csv]);

            self::assertStringContainsString('Import complete', $commandTester->getDisplay());
            self::assertEquals(0, $commandTester->getStatusCode());

            unlink($csv);
        }
    }

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
