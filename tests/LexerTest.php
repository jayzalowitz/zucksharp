<?php

declare(strict_types=1);

namespace ZuckSharp\Tests;

use PHPUnit\Framework\TestCase;
use ZuckSharp\Lexer;
use ZuckSharp\Token;

require_once __DIR__ . '/../src/Lexer.php';

/**
 * Tests for the Zuck# Lexer - tokenizing the language of data harvesting
 */
class LexerTest extends TestCase
{
    /**
     * Test that programs must start with <?zuck - no sneaking in without proper credentials
     */
    public function testMovesFastAndBreaksThingsWithoutOpenTag(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Zuck# programs must start with <?zuck');

        $lexer = new Lexer('SENATOR_WE_RUN_ADS "Hello";');
        $lexer->tokenize();
    }

    /**
     * Test basic open and close tags
     */
    public function testOpenAndCloseTagsTokenized(): void
    {
        $lexer = new Lexer('<?zuck ?>');
        $tokens = $lexer->tokenize();

        $this->assertCount(3, $tokens);
        $this->assertEquals('OPEN_TAG', $tokens[0]->type);
        $this->assertEquals('CLOSE_TAG', $tokens[1]->type);
        $this->assertEquals('EOF', $tokens[2]->type);
    }

    /**
     * Test SENATOR_WE_RUN_ADS (echo) tokenization
     */
    public function testSenatorWeRunAdsTokenization(): void
    {
        $lexer = new Lexer('<?zuck SENATOR_WE_RUN_ADS "Hello"; ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'ECHO', 'STRING', 'SEMICOLON', 'CLOSE_TAG', 'EOF'], $tokens);
        $this->assertEquals('Hello', $tokens[2]->value);
    }

    /**
     * Test STEAL_DATA (variable assignment) tokenization
     */
    public function testStealDataTokenization(): void
    {
        $lexer = new Lexer('<?zuck STEAL_DATA $userData = "everything"; ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'ASSIGN', 'VARIABLE', 'ASSIGN_OP', 'STRING', 'SEMICOLON', 'CLOSE_TAG', 'EOF'], $tokens);
        $this->assertEquals('$userData', $tokens[2]->value);
    }

    /**
     * Test all boolean and null keywords
     */
    public function testConnectedDisconnectedMyspaceTokenization(): void
    {
        $lexer = new Lexer('<?zuck CONNECTED DISCONNECTED MYSPACE ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'TRUE', 'FALSE', 'NULL', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test conditional keywords
     */
    public function testPivotKeywordsTokenization(): void
    {
        $lexer = new Lexer('<?zuck PIVOT_TO_VIDEO PIVOT_TO_REELS PIVOT_TO_METAVERSE END_PIVOT ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'IF', 'ELSEIF', 'ELSE', 'ENDIF', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test loop keywords
     */
    public function testLoopKeywordsTokenization(): void
    {
        $lexer = new Lexer('<?zuck MOVE_FAST BREAK_THINGS GROWTH_HACK PLATEAU HARVEST_USERS USERS_HARVESTED ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'WHILE', 'ENDWHILE', 'FOR', 'ENDFOR', 'FOREACH', 'ENDFOREACH', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test function keywords
     */
    public function testFeatureAndIpoTokenization(): void
    {
        $lexer = new Lexer('<?zuck FEATURE IPO ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'FUNCTION', 'RETURN', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test class-related keywords
     */
    public function testCorporationKeywordsTokenization(): void
    {
        $lexer = new Lexer('<?zuck CORPORATION ACQUIRE OPEN_GRAPH SHADOW_PROFILE THE_ZUCC ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'CLASS', 'NEW', 'PUBLIC', 'PRIVATE', 'THIS', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test error handling keywords
     */
    public function testCongressionalHearingKeywordsTokenization(): void
    {
        $lexer = new Lexer('<?zuck CONGRESSIONAL_HEARING TAKE_RESPONSIBILITY BLAME_RUSSIA ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'TRY', 'CATCH', 'THROW', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test operator tokenization
     */
    public function testZuckOperatorsTokenization(): void
    {
        $lexer = new Lexer('<?zuck IS_CONNECTED_TO UNFRIENDED AND_ALSO_YOUR_DATA OR_YOUR_FRIENDS_DATA ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'EQ', 'NEQ', 'AND', 'OR', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test increment/decrement operators
     */
    public function testEngagementAndChurnTokenization(): void
    {
        $lexer = new Lexer('<?zuck ENGAGEMENT CHURN ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'INCREMENT', 'DECREMENT', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test magic constants
     */
    public function testMagicConstantsTokenization(): void
    {
        $lexer = new Lexer('<?zuck ZUCKS_AGE SWEET_BABY_RAYS ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'MAGIC_BIRTHYEAR', 'MAGIC_BBQ', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test string tokenization with escape sequences
     */
    public function testStringWithEscapeSequences(): void
    {
        $lexer = new Lexer('<?zuck "Hello\nWorld\t!" ?>');
        $tokens = $lexer->tokenize();

        $this->assertEquals("Hello\nWorld\t!", $tokens[1]->value);
    }

    /**
     * Test integer tokenization
     */
    public function testIntegerTokenization(): void
    {
        $lexer = new Lexer('<?zuck 42 3000000000 ?>');
        $tokens = $lexer->tokenize();

        $this->assertEquals('INTEGER', $tokens[1]->type);
        $this->assertEquals(42, $tokens[1]->value);
        $this->assertEquals(3000000000, $tokens[2]->value);
    }

    /**
     * Test float tokenization
     */
    public function testFloatTokenization(): void
    {
        $lexer = new Lexer('<?zuck 3.14 100.5 ?>');
        $tokens = $lexer->tokenize();

        $this->assertEquals('FLOAT', $tokens[1]->type);
        $this->assertEquals(3.14, $tokens[1]->value);
    }

    /**
     * Test variable tokenization
     */
    public function testVariableTokenization(): void
    {
        $lexer = new Lexer('<?zuck $userData $privacy_setting $totalDAUs ?>');
        $tokens = $lexer->tokenize();

        $this->assertEquals('VARIABLE', $tokens[1]->type);
        $this->assertEquals('$userData', $tokens[1]->value);
        $this->assertEquals('$privacy_setting', $tokens[2]->value);
        $this->assertEquals('$totalDAUs', $tokens[3]->value);
    }

    /**
     * Test REDACTED comments are skipped
     */
    public function testRedactedCommentsSkipped(): void
    {
        $lexer = new Lexer("<?zuck REDACTED This is a secret comment\nSENATOR_WE_RUN_ADS \"Hello\"; ?>");
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'ECHO', 'STRING', 'SEMICOLON', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test // style comments are skipped
     */
    public function testSlashCommentsSkipped(): void
    {
        $lexer = new Lexer("<?zuck // This is a comment\nSENATOR_WE_RUN_ADS \"Hello\"; ?>");
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'ECHO', 'STRING', 'SEMICOLON', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test SOCIAL_GRAPH (array) tokenization
     */
    public function testSocialGraphTokenization(): void
    {
        $lexer = new Lexer('<?zuck SOCIAL_GRAPH["a", "b"] ?>');
        $tokens = $lexer->tokenize();

        $this->assertEquals('ARRAY', $tokens[1]->type);
    }

    /**
     * Test arrow operator tokenization
     */
    public function testArrowOperatorTokenization(): void
    {
        $lexer = new Lexer('<?zuck -> => ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'ARROW', 'DOUBLE_ARROW', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test built-in function keywords
     */
    public function testBuiltinFunctionTokenization(): void
    {
        $lexer = new Lexer('<?zuck COLLECT MONETIZE COUNT_USERS BOOST ALGORITHM ?>');
        $tokens = $lexer->tokenize();

        $this->assertTokenTypes(['OPEN_TAG', 'BUILTIN_INPUT', 'BUILTIN_TOSTRING', 'BUILTIN_COUNT', 'BUILTIN_BOOST', 'BUILTIN_SORT', 'CLOSE_TAG', 'EOF'], $tokens);
    }

    /**
     * Test line number tracking
     */
    public function testLineNumberTracking(): void
    {
        $lexer = new Lexer("<?zuck\nSENATOR_WE_RUN_ADS\n\"Hello\"; ?>");
        $tokens = $lexer->tokenize();

        $this->assertEquals(1, $tokens[0]->line); // OPEN_TAG
        $this->assertEquals(2, $tokens[1]->line); // ECHO
        $this->assertEquals(3, $tokens[2]->line); // STRING
    }

    /**
     * Test unterminated string throws exception
     */
    public function testUnterminatedStringThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unterminated string');

        $lexer = new Lexer('<?zuck "Hello ?>');
        $lexer->tokenize();
    }

    /**
     * Helper to assert token types match expected sequence
     */
    private function assertTokenTypes(array $expected, array $tokens): void
    {
        $actual = array_map(fn(Token $t) => $t->type, $tokens);
        $this->assertEquals($expected, $actual);
    }
}
