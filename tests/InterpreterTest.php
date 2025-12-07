<?php

declare(strict_types=1);

namespace ZuckSharp\Tests;

use PHPUnit\Framework\TestCase;
use ZuckSharp\ZuckSharp;

require_once __DIR__ . '/../src/ZuckSharp.php';

/**
 * Tests for the Zuck# Interpreter - executing your data harvesting dreams
 */
class InterpreterTest extends TestCase
{
    private ZuckSharp $zuck;

    protected function setUp(): void
    {
        $this->zuck = new ZuckSharp();
    }

    /**
     * Execute source code and return output
     */
    private function execute(string $source): string
    {
        return $this->zuck->run($source);
    }

    /**
     * Test SENATOR_WE_RUN_ADS outputs text
     */
    public function testSenatorWeRunAdsOutputsText(): void
    {
        $output = $this->execute('<?zuck SENATOR_WE_RUN_ADS "Hello, World!"; ?>');
        $this->assertEquals('Hello, World!', $output);
    }

    /**
     * Test POKE outputs text with newline
     */
    public function testPokeOutputsWithNewline(): void
    {
        $output = $this->execute('<?zuck POKE "Hello"; ?>');
        $this->assertEquals("Hello\n", $output);
    }

    /**
     * Test variable assignment and retrieval
     */
    public function testStealDataAndRetrieve(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $msg = "Harvested!";
            SENATOR_WE_RUN_ADS $msg;
        ?>');
        $this->assertEquals('Harvested!', $output);
    }

    /**
     * Test boolean CONNECTED (true)
     */
    public function testConnectedIsTrue(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $human = CONNECTED;
            SENATOR_WE_RUN_ADS $human;
        ?>');
        $this->assertEquals('CONNECTED', $output);
    }

    /**
     * Test boolean DISCONNECTED (false)
     */
    public function testDisconnectedIsFalse(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $human = DISCONNECTED;
            SENATOR_WE_RUN_ADS $human;
        ?>');
        $this->assertEquals('DISCONNECTED', $output);
    }

    /**
     * Test MYSPACE (null)
     */
    public function testMyspaceIsNull(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $empathy = MYSPACE;
            SENATOR_WE_RUN_ADS $empathy;
        ?>');
        $this->assertEquals('MYSPACE', $output);
    }

    /**
     * Test arithmetic operations
     */
    public function testArithmeticOperations(): void
    {
        $output = $this->execute('<?zuck SENATOR_WE_RUN_ADS 2 + 3 * 4; ?>');
        $this->assertEquals('14', $output);
    }

    /**
     * Test string concatenation with dot operator
     */
    public function testStringConcatenation(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $name = "Mark";
            SENATOR_WE_RUN_ADS "Hello, " . $name . "!";
        ?>');
        $this->assertEquals('Hello, Mark!', $output);
    }

    /**
     * Test modulo with REMAINDER_OF_PRIVACY
     */
    public function testRemainderOfPrivacy(): void
    {
        $output = $this->execute('<?zuck SENATOR_WE_RUN_ADS 10 REMAINDER_OF_PRIVACY 3; ?>');
        $this->assertEquals('1', $output);
    }

    /**
     * Test IS_CONNECTED_TO (equality)
     */
    public function testIsConnectedTo(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $result = 5 IS_CONNECTED_TO 5;
            SENATOR_WE_RUN_ADS $result;
        ?>');
        $this->assertEquals('CONNECTED', $output);
    }

    /**
     * Test UNFRIENDED (not equal)
     */
    public function testUnfriended(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $result = 5 UNFRIENDED 3;
            SENATOR_WE_RUN_ADS $result;
        ?>');
        $this->assertEquals('CONNECTED', $output);
    }

    /**
     * Test PIVOT_TO_VIDEO (if true branch)
     */
    public function testPivotToVideoTrueBranch(): void
    {
        $output = $this->execute('<?zuck
            PIVOT_TO_VIDEO (CONNECTED) {
                SENATOR_WE_RUN_ADS "In the if!";
            } END_PIVOT
        ?>');
        $this->assertEquals('In the if!', $output);
    }

    /**
     * Test PIVOT_TO_METAVERSE (else branch)
     */
    public function testPivotToMetaverseElseBranch(): void
    {
        $output = $this->execute('<?zuck
            PIVOT_TO_VIDEO (DISCONNECTED) {
                SENATOR_WE_RUN_ADS "if";
            } PIVOT_TO_METAVERSE {
                SENATOR_WE_RUN_ADS "else";
            } END_PIVOT
        ?>');
        $this->assertEquals('else', $output);
    }

    /**
     * Test PIVOT_TO_REELS (elseif branch)
     */
    public function testPivotToReelsElseIfBranch(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $x = 50;
            PIVOT_TO_VIDEO ($x > 100) {
                SENATOR_WE_RUN_ADS "A";
            } PIVOT_TO_REELS ($x > 25) {
                SENATOR_WE_RUN_ADS "B";
            } PIVOT_TO_METAVERSE {
                SENATOR_WE_RUN_ADS "C";
            } END_PIVOT
        ?>');
        $this->assertEquals('B', $output);
    }

    /**
     * Test MOVE_FAST (while loop)
     */
    public function testMoveFastWhileLoop(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $i = 0;
            MOVE_FAST ($i < 3) {
                SENATOR_WE_RUN_ADS $i;
                $i ENGAGEMENT;
            } BREAK_THINGS
        ?>');
        $this->assertEquals('012', $output);
    }

    /**
     * Test GROWTH_HACK (for loop)
     */
    public function testGrowthHackForLoop(): void
    {
        $output = $this->execute('<?zuck
            GROWTH_HACK ($i = 1; $i <= 3; $i ENGAGEMENT) {
                SENATOR_WE_RUN_ADS $i;
            } PLATEAU
        ?>');
        $this->assertEquals('123', $output);
    }

    /**
     * Test HARVEST_USERS (foreach loop)
     */
    public function testHarvestUsersForeachLoop(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $users = SOCIAL_GRAPH["A", "B", "C"];
            HARVEST_USERS ($users AS $user) {
                SENATOR_WE_RUN_ADS $user;
            } USERS_HARVESTED
        ?>');
        $this->assertEquals('ABC', $output);
    }

    /**
     * Test ENGAGEMENT (increment)
     */
    public function testEngagementIncrement(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $users = 0;
            $users ENGAGEMENT;
            $users ENGAGEMENT;
            SENATOR_WE_RUN_ADS $users;
        ?>');
        $this->assertEquals('2', $output);
    }

    /**
     * Test CHURN (decrement)
     */
    public function testChurnDecrement(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $users = 5;
            $users CHURN;
            SENATOR_WE_RUN_ADS $users;
        ?>');
        $this->assertEquals('4', $output);
    }

    /**
     * Test FEATURE and function call
     */
    public function testFeatureAndFunctionCall(): void
    {
        $output = $this->execute('<?zuck
            FEATURE greet($name) {
                IPO "Hello, " . $name . "!";
            }
            SENATOR_WE_RUN_ADS greet("Senator");
        ?>');
        $this->assertEquals('Hello, Senator!', $output);
    }

    /**
     * Test function with return value
     */
    public function testFunctionWithReturnValue(): void
    {
        $output = $this->execute('<?zuck
            FEATURE add($a, $b) {
                IPO $a + $b;
            }
            STEAL_DATA $sum = add(2, 3);
            SENATOR_WE_RUN_ADS $sum;
        ?>');
        $this->assertEquals('5', $output);
    }

    /**
     * Test CORPORATION (class) and ACQUIRE (new)
     */
    public function testCorporationAndAcquire(): void
    {
        $output = $this->execute('<?zuck
            CORPORATION Greeter {
                OPEN_GRAPH FEATURE sayHi() {
                    SENATOR_WE_RUN_ADS "Hi from Corporation!";
                }
            }
            STEAL_DATA $g = ACQUIRE Greeter();
            $g->sayHi();
        ?>');
        $this->assertEquals('Hi from Corporation!', $output);
    }

    /**
     * Test class with constructor
     */
    public function testClassWithConstructor(): void
    {
        $output = $this->execute('<?zuck
            CORPORATION Network {
                OPEN_GRAPH FEATURE __construct($name) {
                    SENATOR_WE_RUN_ADS "Created: " . $name;
                }
            }
            STEAL_DATA $fb = ACQUIRE Network("Facebook");
        ?>');
        $this->assertEquals('Created: Facebook', $output);
    }

    /**
     * Test CONGRESSIONAL_HEARING (try-catch)
     */
    public function testCongressionalHearingTryCatch(): void
    {
        $output = $this->execute('<?zuck
            CONGRESSIONAL_HEARING {
                BLAME_RUSSIA ACQUIRE Exception("Data breach!");
            } TAKE_RESPONSIBILITY (Exception $e) {
                SENATOR_WE_RUN_ADS "Caught!";
            }
        ?>');
        $this->assertEquals('Caught!', $output);
    }

    /**
     * Test exception not thrown - try block completes
     */
    public function testTryBlockCompletesWithoutException(): void
    {
        $output = $this->execute('<?zuck
            CONGRESSIONAL_HEARING {
                SENATOR_WE_RUN_ADS "No problem!";
            } TAKE_RESPONSIBILITY (Exception $e) {
                SENATOR_WE_RUN_ADS "Caught!";
            }
        ?>');
        $this->assertEquals('No problem!', $output);
    }

    /**
     * Test SOCIAL_GRAPH (array) creation and access
     */
    public function testSocialGraphArrayAccess(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $arr = SOCIAL_GRAPH["a", "b", "c"];
            SENATOR_WE_RUN_ADS $arr[1];
        ?>');
        $this->assertEquals('b', $output);
    }

    /**
     * Test associative array access
     */
    public function testAssociativeArrayAccess(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $user = SOCIAL_GRAPH["name" => "Mark", "role" => "CEO"];
            SENATOR_WE_RUN_ADS $user["name"];
        ?>');
        $this->assertEquals('Mark', $output);
    }

    /**
     * Test ZUCKS_AGE magic constant
     */
    public function testZucksAgeMagicConstant(): void
    {
        $output = $this->execute('<?zuck SENATOR_WE_RUN_ADS ZUCKS_AGE; ?>');
        $this->assertEquals('2000', $output);
    }

    /**
     * Test SWEET_BABY_RAYS magic constant
     */
    public function testSweetBabyRaysMagicConstant(): void
    {
        $output = $this->execute('<?zuck SENATOR_WE_RUN_ADS SWEET_BABY_RAYS; ?>');
        $this->assertEquals('BBQ', $output);
    }

    /**
     * Test COUNT_USERS builtin function
     */
    public function testCountUsersBuiltin(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $arr = SOCIAL_GRAPH[1, 2, 3, 4, 5];
            SENATOR_WE_RUN_ADS COUNT_USERS($arr);
        ?>');
        $this->assertEquals('5', $output);
    }

    /**
     * Test MONETIZE builtin function
     */
    public function testMonetizeBuiltin(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $num = 42;
            STEAL_DATA $str = MONETIZE($num);
            SENATOR_WE_RUN_ADS $str . " is a string";
        ?>');
        $this->assertEquals('42 is a string', $output);
    }

    /**
     * Test FACT_CHECK_THIS builtin function
     */
    public function testFactCheckThisBuiltin(): void
    {
        $output = $this->execute('<?zuck
            SENATOR_WE_RUN_ADS FACT_CHECK_THIS(1);
            SENATOR_WE_RUN_ADS FACT_CHECK_THIS(0);
        ?>');
        $this->assertEquals('CONNECTEDDISCONNECTED', $output);
    }

    /**
     * Test AND_ALSO_YOUR_DATA (logical AND)
     */
    public function testAndAlsoYourData(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $result = CONNECTED AND_ALSO_YOUR_DATA CONNECTED;
            SENATOR_WE_RUN_ADS $result;
        ?>');
        $this->assertEquals('CONNECTED', $output);
    }

    /**
     * Test OR_YOUR_FRIENDS_DATA (logical OR)
     */
    public function testOrYourFriendsData(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $result = DISCONNECTED OR_YOUR_FRIENDS_DATA CONNECTED;
            SENATOR_WE_RUN_ADS $result;
        ?>');
        $this->assertEquals('CONNECTED', $output);
    }

    /**
     * Test FAKE_NEWS (logical NOT)
     */
    public function testFakeNewsLogicalNot(): void
    {
        $output = $this->execute('<?zuck
            STEAL_DATA $result = FAKE_NEWS DISCONNECTED;
            SENATOR_WE_RUN_ADS $result;
        ?>');
        $this->assertEquals('CONNECTED', $output);
    }

    /**
     * Test RAGE_QUIT (break)
     */
    public function testRageQuitBreak(): void
    {
        $output = $this->execute('<?zuck
            GROWTH_HACK ($i = 0; $i < 10; $i ENGAGEMENT) {
                PIVOT_TO_VIDEO ($i IS_CONNECTED_TO 3) {
                    RAGE_QUIT;
                } END_PIVOT
                SENATOR_WE_RUN_ADS $i;
            } PLATEAU
        ?>');
        $this->assertEquals('012', $output);
    }

    /**
     * Test SCROLL_PAST (continue)
     */
    public function testScrollPastContinue(): void
    {
        $output = $this->execute('<?zuck
            GROWTH_HACK ($i = 0; $i < 5; $i ENGAGEMENT) {
                PIVOT_TO_VIDEO ($i IS_CONNECTED_TO 2) {
                    SCROLL_PAST;
                } END_PIVOT
                SENATOR_WE_RUN_ADS $i;
            } PLATEAU
        ?>');
        $this->assertEquals('0134', $output);
    }

    /**
     * Test nested loops
     */
    public function testNestedLoops(): void
    {
        $output = $this->execute('<?zuck
            GROWTH_HACK ($i = 0; $i < 2; $i ENGAGEMENT) {
                GROWTH_HACK ($j = 0; $j < 2; $j ENGAGEMENT) {
                    SENATOR_WE_RUN_ADS $i . $j;
                } PLATEAU
            } PLATEAU
        ?>');
        $this->assertEquals('00011011', $output);
    }

    /**
     * Test recursive function
     */
    public function testRecursiveFunction(): void
    {
        $output = $this->execute('<?zuck
            FEATURE factorial($n) {
                PIVOT_TO_VIDEO ($n <= 1) {
                    IPO 1;
                } END_PIVOT
                IPO $n * factorial($n - 1);
            }
            SENATOR_WE_RUN_ADS factorial(5);
        ?>');
        $this->assertEquals('120', $output);
    }

    /**
     * Test error message format
     */
    public function testErrorMessageFormat(): void
    {
        $output = $this->execute('<?zuck SENATOR_WE_RUN_ADS $undefined; ?>');
        $this->assertStringContainsString('ZUCK# ERROR', $output);
    }

    /**
     * Test division by zero returns 0
     */
    public function testDivisionByZeroReturnsZero(): void
    {
        $output = $this->execute('<?zuck SENATOR_WE_RUN_ADS 10 / 0; ?>');
        $this->assertEquals('0', $output);
    }

    /**
     * Test comparison operators
     */
    public function testComparisonOperators(): void
    {
        $output = $this->execute('<?zuck
            SENATOR_WE_RUN_ADS 5 > 3;
            SENATOR_WE_RUN_ADS 5 < 3;
            SENATOR_WE_RUN_ADS 5 >= 5;
            SENATOR_WE_RUN_ADS 5 <= 4;
        ?>');
        $this->assertEquals('CONNECTEDDISCONNECTEDCONNECTEDDISCONNECTED', $output);
    }
}
