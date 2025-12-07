<?php

declare(strict_types=1);

namespace ZuckSharp\Tests;

use PHPUnit\Framework\TestCase;
use ZuckSharp\ArrayExpression;
use ZuckSharp\AssignmentStatement;
use ZuckSharp\BinaryExpression;
use ZuckSharp\ClassDeclaration;
use ZuckSharp\EchoStatement;
use ZuckSharp\ForeachStatement;
use ZuckSharp\ForStatement;
use ZuckSharp\FunctionCall;
use ZuckSharp\FunctionDeclaration;
use ZuckSharp\IfStatement;
use ZuckSharp\Lexer;
use ZuckSharp\LiteralExpression;
use ZuckSharp\NewExpression;
use ZuckSharp\Parser;
use ZuckSharp\PrintStatement;
use ZuckSharp\Program;
use ZuckSharp\ReturnStatement;
use ZuckSharp\TryCatchStatement;
use ZuckSharp\WhileStatement;

require_once __DIR__ . '/../src/Lexer.php';
require_once __DIR__ . '/../src/Parser.php';

/**
 * Tests for the Zuck# Parser - building the AST of your data empire
 */
class ParserTest extends TestCase
{
    /**
     * Parse source code and return the AST
     */
    private function parse(string $source): Program
    {
        $lexer = new Lexer($source);
        $tokens = $lexer->tokenize();
        $parser = new Parser($tokens);

        return $parser->parse();
    }

    /**
     * Test parsing empty program
     */
    public function testParseEmptyProgram(): void
    {
        $program = $this->parse('<?zuck ?>');

        $this->assertInstanceOf(Program::class, $program);
        $this->assertCount(0, $program->statements);
    }

    /**
     * Test parsing SENATOR_WE_RUN_ADS (echo) statement
     */
    public function testParseSenatorWeRunAds(): void
    {
        $program = $this->parse('<?zuck SENATOR_WE_RUN_ADS "Hello, Senator!"; ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(EchoStatement::class, $program->statements[0]);
        $this->assertInstanceOf(LiteralExpression::class, $program->statements[0]->expression);
        $this->assertEquals('Hello, Senator!', $program->statements[0]->expression->value);
    }

    /**
     * Test parsing POKE (print) statement
     */
    public function testParsePoke(): void
    {
        $program = $this->parse('<?zuck POKE "Your data is safe"; ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(PrintStatement::class, $program->statements[0]);
    }

    /**
     * Test parsing STEAL_DATA (variable assignment)
     */
    public function testParseStealData(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $userData = "everything"; ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(AssignmentStatement::class, $program->statements[0]);
        $this->assertEquals('$userData', $program->statements[0]->variable);
    }

    /**
     * Test parsing boolean literals
     */
    public function testParseBooleanLiterals(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $connected = CONNECTED; STEAL_DATA $disconnected = DISCONNECTED; ?>');

        $this->assertCount(2, $program->statements);

        $this->assertInstanceOf(LiteralExpression::class, $program->statements[0]->value);
        $this->assertTrue($program->statements[0]->value->value);

        $this->assertInstanceOf(LiteralExpression::class, $program->statements[1]->value);
        $this->assertFalse($program->statements[1]->value->value);
    }

    /**
     * Test parsing MYSPACE (null)
     */
    public function testParseMyspace(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $empathy = MYSPACE; ?>');

        $this->assertInstanceOf(LiteralExpression::class, $program->statements[0]->value);
        $this->assertNull($program->statements[0]->value->value);
    }

    /**
     * Test parsing PIVOT_TO_VIDEO (if statement)
     */
    public function testParsePivotToVideo(): void
    {
        $program = $this->parse('<?zuck
            PIVOT_TO_VIDEO ($stock > 300) {
                SENATOR_WE_RUN_ADS "Shareholders happy!";
            } END_PIVOT
        ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(IfStatement::class, $program->statements[0]);
        $this->assertCount(1, $program->statements[0]->thenBranch);
    }

    /**
     * Test parsing if-elseif-else chain
     */
    public function testParsePivotChain(): void
    {
        $program = $this->parse('<?zuck
            PIVOT_TO_VIDEO ($x > 100) {
                SENATOR_WE_RUN_ADS "A";
            } PIVOT_TO_REELS ($x > 50) {
                SENATOR_WE_RUN_ADS "B";
            } PIVOT_TO_METAVERSE {
                SENATOR_WE_RUN_ADS "C";
            } END_PIVOT
        ?>');

        $stmt = $program->statements[0];
        $this->assertInstanceOf(IfStatement::class, $stmt);
        $this->assertCount(1, $stmt->elseIfBranches);
        $this->assertNotNull($stmt->elseBranch);
    }

    /**
     * Test parsing MOVE_FAST (while loop)
     */
    public function testParseMoveFast(): void
    {
        $program = $this->parse('<?zuck
            MOVE_FAST ($users < 3000000000) {
                $users ENGAGEMENT;
            } BREAK_THINGS
        ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(WhileStatement::class, $program->statements[0]);
    }

    /**
     * Test parsing GROWTH_HACK (for loop)
     */
    public function testParseGrowthHack(): void
    {
        $program = $this->parse('<?zuck
            GROWTH_HACK ($i = 1; $i <= 10; $i ENGAGEMENT) {
                SENATOR_WE_RUN_ADS $i;
            } PLATEAU
        ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(ForStatement::class, $program->statements[0]);
        $this->assertNotNull($program->statements[0]->init);
        $this->assertNotNull($program->statements[0]->condition);
        $this->assertNotNull($program->statements[0]->increment);
    }

    /**
     * Test parsing HARVEST_USERS (foreach loop)
     */
    public function testParseHarvestUsers(): void
    {
        $program = $this->parse('<?zuck
            STEAL_DATA $users = SOCIAL_GRAPH["Tom", "Eduardo"];
            HARVEST_USERS ($users AS $user) {
                SENATOR_WE_RUN_ADS $user;
            } USERS_HARVESTED
        ?>');

        $this->assertCount(2, $program->statements);
        $this->assertInstanceOf(ForeachStatement::class, $program->statements[1]);
        $this->assertEquals('$user', $program->statements[1]->valueVar);
    }

    /**
     * Test parsing FEATURE (function declaration)
     */
    public function testParseFeature(): void
    {
        $program = $this->parse('<?zuck
            FEATURE greet($name) {
                IPO "Hello, " . $name;
            }
        ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(FunctionDeclaration::class, $program->statements[0]);
        $this->assertEquals('greet', $program->statements[0]->name);
        $this->assertEquals(['$name'], $program->statements[0]->params);
    }

    /**
     * Test parsing function call
     */
    public function testParseFunctionCall(): void
    {
        $program = $this->parse('<?zuck greet("Senator"); ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(FunctionCall::class, $program->statements[0]);
        $this->assertEquals('greet', $program->statements[0]->name);
    }

    /**
     * Test parsing IPO (return statement)
     */
    public function testParseIpo(): void
    {
        $program = $this->parse('<?zuck
            FEATURE getValue() {
                IPO 42;
            }
        ?>');

        $func = $program->statements[0];
        $this->assertInstanceOf(ReturnStatement::class, $func->body[0]);
    }

    /**
     * Test parsing CORPORATION (class declaration)
     */
    public function testParseCorporation(): void
    {
        $program = $this->parse('<?zuck
            CORPORATION SocialNetwork {
                OPEN_GRAPH $name;
                SHADOW_PROFILE $secrets;

                OPEN_GRAPH FEATURE __construct($name) {
                    SENATOR_WE_RUN_ADS $name;
                }
            }
        ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(ClassDeclaration::class, $program->statements[0]);
        $this->assertEquals('SocialNetwork', $program->statements[0]->name);
        $this->assertCount(3, $program->statements[0]->members);
    }

    /**
     * Test parsing ACQUIRE (new instance)
     */
    public function testParseAcquire(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $fb = ACQUIRE SocialNetwork("Facebook"); ?>');

        $this->assertInstanceOf(NewExpression::class, $program->statements[0]->value);
        $this->assertEquals('SocialNetwork', $program->statements[0]->value->className);
    }

    /**
     * Test parsing CONGRESSIONAL_HEARING (try-catch)
     */
    public function testParseCongressionalHearing(): void
    {
        $program = $this->parse('<?zuck
            CONGRESSIONAL_HEARING {
                BLAME_RUSSIA ACQUIRE Exception("Oops!");
            } TAKE_RESPONSIBILITY (Exception $e) {
                SENATOR_WE_RUN_ADS "We take responsibility.";
            }
        ?>');

        $this->assertCount(1, $program->statements);
        $this->assertInstanceOf(TryCatchStatement::class, $program->statements[0]);
        $this->assertEquals('$e', $program->statements[0]->exceptionVar);
    }

    /**
     * Test parsing SOCIAL_GRAPH (array literal)
     */
    public function testParseSocialGraph(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $friends = SOCIAL_GRAPH["Tom", "Eduardo", "Sean"]; ?>');

        $this->assertInstanceOf(ArrayExpression::class, $program->statements[0]->value);
        $this->assertCount(3, $program->statements[0]->value->elements);
    }

    /**
     * Test parsing associative array
     */
    public function testParseAssociativeArray(): void
    {
        $program = $this->parse('<?zuck
            STEAL_DATA $user = SOCIAL_GRAPH[
                "name" => "Mark",
                "human" => DISCONNECTED
            ];
        ?>');

        $arr = $program->statements[0]->value;
        $this->assertInstanceOf(ArrayExpression::class, $arr);
        $this->assertNotNull($arr->elements[0]['key']);
    }

    /**
     * Test parsing binary expressions
     */
    public function testParseBinaryExpressions(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $result = 1 + 2 * 3; ?>');

        $expr = $program->statements[0]->value;
        $this->assertInstanceOf(BinaryExpression::class, $expr);
        $this->assertEquals('+', $expr->operator);
    }

    /**
     * Test parsing string concatenation
     */
    public function testParseStringConcatenation(): void
    {
        $program = $this->parse('<?zuck SENATOR_WE_RUN_ADS "Hello, " . $name . "!"; ?>');

        $expr = $program->statements[0]->expression;
        $this->assertInstanceOf(BinaryExpression::class, $expr);
        $this->assertEquals('.', $expr->operator);
    }

    /**
     * Test parsing comparison operators
     */
    public function testParseComparisonOperators(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $result = $a IS_CONNECTED_TO $b; ?>');

        $expr = $program->statements[0]->value;
        $this->assertInstanceOf(BinaryExpression::class, $expr);
        $this->assertEquals('==', $expr->operator);
    }

    /**
     * Test parsing logical operators
     */
    public function testParseLogicalOperators(): void
    {
        $program = $this->parse('<?zuck STEAL_DATA $result = $a AND_ALSO_YOUR_DATA $b; ?>');

        $expr = $program->statements[0]->value;
        $this->assertInstanceOf(BinaryExpression::class, $expr);
        $this->assertEquals('&&', $expr->operator);
    }

    /**
     * Test parsing magic constants
     */
    public function testParseMagicConstants(): void
    {
        $program = $this->parse('<?zuck
            STEAL_DATA $year = ZUCKS_AGE;
            STEAL_DATA $sauce = SWEET_BABY_RAYS;
        ?>');

        $this->assertInstanceOf(LiteralExpression::class, $program->statements[0]->value);
        $this->assertEquals(2000, $program->statements[0]->value->value);

        $this->assertInstanceOf(LiteralExpression::class, $program->statements[1]->value);
        $this->assertEquals('BBQ', $program->statements[1]->value->value);
    }

    /**
     * Test parsing multiple statements
     */
    public function testParseMultipleStatements(): void
    {
        $program = $this->parse('<?zuck
            STEAL_DATA $a = 1;
            STEAL_DATA $b = 2;
            SENATOR_WE_RUN_ADS $a + $b;
        ?>');

        $this->assertCount(3, $program->statements);
    }

    /**
     * Test parser error for unexpected token
     */
    public function testParserErrorForUnexpectedToken(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Expected');

        $this->parse('<?zuck STEAL_DATA = "value"; ?>');
    }
}
