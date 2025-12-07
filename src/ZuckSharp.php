<?php

declare(strict_types=1);

namespace ZuckSharp;

require_once __DIR__ . '/Lexer.php';
require_once __DIR__ . '/Parser.php';
require_once __DIR__ . '/Interpreter.php';

class ZuckSharp
{
    private bool $debug = false;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function run(string $source): string
    {
        try {
            // Lexing
            if ($this->debug) {
                echo "=== TOKENIZING ===\n";
            }

            $lexer = new Lexer($source);
            $tokens = $lexer->tokenize();

            if ($this->debug) {
                foreach ($tokens as $token) {
                    echo "  {$token}\n";
                }
                echo "\n=== PARSING ===\n";
            }

            // Parsing
            $parser = new Parser($tokens);
            $ast = $parser->parse();

            if ($this->debug) {
                echo "  AST generated with " . count($ast->statements) . " statements\n";
                echo "\n=== EXECUTING ===\n";
            }

            // Interpreting
            $interpreter = new Interpreter();
            $output = $interpreter->interpret($ast);

            return $output;

        } catch (\Exception $e) {
            return "💀 ZUCK# ERROR: " . $e->getMessage() . "\n" .
                   "Senator, something went wrong.\n";
        }
    }

    public function runFile(string $filename): string
    {
        if (!file_exists($filename)) {
            return "💀 ZUCK# ERROR: File not found: {$filename}\n" .
                   "We couldn't locate that data. Unlike your user data, which we always know where to find.\n";
        }

        $source = file_get_contents($filename);
        return $this->run($source);
    }

    public static function banner(): string
    {
        return <<<'BANNER'
╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║   ███████╗██╗   ██╗ ██████╗██╗  ██╗    ██╗  ██╗                   ║
║   ╚══███╔╝██║   ██║██╔════╝██║ ██╔╝    ██║  ██║                   ║
║     ███╔╝ ██║   ██║██║     █████╔╝     ███████║                   ║
║    ███╔╝  ██║   ██║██║     ██╔═██╗     ╚════██║                   ║
║   ███████╗╚██████╔╝╚██████╗██║  ██╗         ██║                   ║
║   ╚══════╝ ╚═════╝  ╚═════╝╚═╝  ╚═╝         ╚═╝                   ║
║                                                                   ║
║   Move Fast. Break Things. Harvest Data.                          ║
║   A PHP-inspired language for the Metaverse generation.           ║
║                                                                   ║
║   "Senator, we run ads." - The Zucc, 2018                         ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝

BANNER;
    }

    public static function help(): string
    {
        return <<<'HELP'
Usage: zuck <file.zuck> [options]

Options:
  --debug, -d     Show lexer and parser output
  --help, -h      Show this help message
  --version, -v   Show version information

Examples:
  zuck hello.zuck
  zuck --debug program.zuck

File extensions: .zuck, .metaverse

For language documentation, see LANGUAGE.md
For more information, consult your congressional representative.

HELP;
    }

    public static function version(): string
    {
        return "Zuck# v1.0.0 (Move Fast Edition)\n" .
               "Built with PHP " . PHP_VERSION . "\n" .
               "© 2024 Definitely Not Meta\n";
    }
}
