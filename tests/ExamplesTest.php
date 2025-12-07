<?php

declare(strict_types=1);

namespace ZuckSharp\Tests;

use PHPUnit\Framework\TestCase;
use ZuckSharp\ZuckSharp;

require_once __DIR__ . '/../src/ZuckSharp.php';

/**
 * Integration tests - verify all example files run correctly
 * "Move fast and run all the examples" - probably Zuck
 */
class ExamplesTest extends TestCase
{
    private ZuckSharp $zuck;
    private string $examplesDir;

    protected function setUp(): void
    {
        $this->zuck = new ZuckSharp();
        $this->examplesDir = __DIR__ . '/../examples/';
    }

    /**
     * Test hello.zuck runs and outputs expected text
     */
    public function testHelloWorldExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'hello.zuck');

        $this->assertStringContainsString('Hello, World!', $output);
        $this->assertStringContainsString('Your data is safe with us', $output);
    }

    /**
     * Test variables.zuck runs and demonstrates variable usage
     */
    public function testVariablesExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'variables.zuck');

        $this->assertStringContainsString('Name: Mark', $output);
        $this->assertStringContainsString('Company: Meta', $output);
        $this->assertStringContainsString('formerly Facebook', $output);
        $this->assertStringContainsString('Is Human: CONNECTED', $output);
        $this->assertStringContainsString('Empathy Level: MYSPACE', $output);
        $this->assertStringContainsString('Birth Year: 2000', $output);
        $this->assertStringContainsString('Favorite Sauce: BBQ', $output);
    }

    /**
     * Test loops.zuck runs and demonstrates loop constructs
     */
    public function testLoopsExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'loops.zuck');

        // Check while loop output
        $this->assertStringContainsString('Growing User Base', $output);
        $this->assertStringContainsString('Users: 1 billion', $output);
        $this->assertStringContainsString('Users: 5 billion', $output);

        // Check for loop output
        $this->assertStringContainsString('Congressional Hearings Survived', $output);
        $this->assertStringContainsString('Hearing #1', $output);
        $this->assertStringContainsString('Senator, we run ads', $output);

        // Check foreach loop output
        $this->assertStringContainsString('Pivoting Through Strategies', $output);
        $this->assertStringContainsString('Pivoting to: Video', $output);
        $this->assertStringContainsString('Pivoting to: Metaverse', $output);
    }

    /**
     * Test conditionals.zuck runs and demonstrates if/elseif/else
     */
    public function testConditionalsExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'conditionals.zuck');

        // Stock price is 250, so should hit the > 200 branch
        $this->assertStringContainsString('Stock Analysis', $output);
        $this->assertStringContainsString('Stock is okay', $output);
        $this->assertStringContainsString('pivot to something new', $output);

        // Congress is asking
        $this->assertStringContainsString('Congressional Response Protocol', $output);
        $this->assertStringContainsString('Senator, we run ads', $output);

        // No data breach today
        $this->assertStringContainsString('Data Breach Status', $output);
        $this->assertStringContainsString('No breaches today', $output);
    }

    /**
     * Test functions.zuck runs and demonstrates function definitions
     */
    public function testFunctionsExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'functions.zuck');

        // Check function calls
        $this->assertStringContainsString('Senate Hearing Simulation', $output);
        $this->assertStringContainsString('Hello, Senator Warren', $output);
        $this->assertStringContainsString('Hello, Senator Cruz', $output);

        // Check calculated value
        $this->assertStringContainsString('User Value Calculation', $output);
        $this->assertStringContainsString('Total user value', $output);

        // Check apologize function
        $this->assertStringContainsString('PR Response', $output);
        $this->assertStringContainsString('Cambridge Analytica', $output);
        $this->assertStringContainsString('We take', $output);
        $this->assertStringContainsString('very seriously', $output);

        // Check pivot function
        $this->assertStringContainsString('Strategic Pivot', $output);
        $this->assertStringContainsString('Pivoting from Social Media to Metaverse', $output);
    }

    /**
     * Test classes.zuck runs and demonstrates OOP features
     */
    public function testClassesExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'classes.zuck');

        // Check class instantiation
        $this->assertStringContainsString('Creating Social Network', $output);
        $this->assertStringContainsString('Creating network: Facebook', $output);

        // Check method calls
        $this->assertStringContainsString('Data Harvesting Operations', $output);
        $this->assertStringContainsString('Harvesting: location', $output);
        $this->assertStringContainsString('Harvesting: contacts', $output);
        $this->assertStringContainsString('Harvesting: face_geometry', $output);

        // Check apologize method
        $this->assertStringContainsString('PR Response Time', $output);
        $this->assertStringContainsString('We take your privacy seriously', $output);

        // Check pivot method
        $this->assertStringContainsString('Pivoting to: Metaverse', $output);
        $this->assertStringContainsString('Laying off 10,000 employees', $output);
    }

    /**
     * Test error_handling.zuck runs and demonstrates try/catch
     */
    public function testErrorHandlingExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'error_handling.zuck');

        // Check try block starts
        $this->assertStringContainsString('Data Breach Simulation', $output);
        $this->assertStringContainsString('Processing user data', $output);
        $this->assertStringContainsString('Sharing with third parties', $output);

        // Check exception is caught
        $this->assertStringContainsString('INCIDENT DETECTED', $output);
        $this->assertStringContainsString('Activating PR response', $output);
        $this->assertStringContainsString('We take this very seriously', $output);

        // Check code after try/catch continues
        $this->assertStringContainsString('Continuing Operations', $output);
        $this->assertStringContainsString('Business as usual', $output);
    }

    /**
     * Test fizzbuzz.zuck runs and outputs correct sequence
     */
    public function testFizzBuzzExample(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'fizzbuzz.zuck');

        $this->assertStringContainsString('FizzBuzz: Zuck Edition', $output);
        $this->assertStringContainsString('Fizz = Pivot, Buzz = Acquire', $output);

        // Check some expected values
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString('2', $output);
        $this->assertStringContainsString('Pivot', $output);  // 3 is divisible by 3
        $this->assertStringContainsString('4', $output);
        $this->assertStringContainsString('Acquire', $output); // 5 is divisible by 5
        $this->assertStringContainsString('PivotAcquire', $output); // 15 is divisible by both
    }

    /**
     * Test all example files exist
     */
    public function testAllExampleFilesExist(): void
    {
        $expectedFiles = [
            'hello.zuck',
            'variables.zuck',
            'loops.zuck',
            'conditionals.zuck',
            'functions.zuck',
            'classes.zuck',
            'error_handling.zuck',
            'fizzbuzz.zuck',
        ];

        foreach ($expectedFiles as $file) {
            $this->assertFileExists(
                $this->examplesDir . $file,
                "Example file {$file} should exist"
            );
        }
    }

    /**
     * Test all example files run without errors
     */
    public function testAllExamplesRunWithoutErrors(): void
    {
        $files = glob($this->examplesDir . '*.zuck');

        foreach ($files as $file) {
            $output = $this->zuck->runFile($file);
            $this->assertStringNotContainsString(
                'ZUCK# ERROR',
                $output,
                "Example " . basename($file) . " should run without errors"
            );
        }
    }

    /**
     * Test running a non-existent file gives helpful error
     */
    public function testNonExistentFileGivesError(): void
    {
        $output = $this->zuck->runFile($this->examplesDir . 'does_not_exist.zuck');

        $this->assertStringContainsString('ZUCK# ERROR', $output);
        $this->assertStringContainsString('File not found', $output);
    }
}
