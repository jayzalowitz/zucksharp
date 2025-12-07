<?php

declare(strict_types=1);

namespace ZuckSharp;

class Token
{
    public function __construct(
        public string $type,
        public mixed $value,
        public int $line,
        public int $column
    ) {}

    public function __toString(): string
    {
        return "Token({$this->type}, {$this->value}, line {$this->line})";
    }
}

class Lexer
{
    private string $source;
    private int $pos = 0;
    private int $line = 1;
    private int $column = 1;
    private int $length;

    // Zuck# keywords mapped to token types
    private array $keywords = [
        // Output
        'SENATOR_WE_RUN_ADS' => 'ECHO',
        'POKE' => 'PRINT',

        // Variables
        'STEAL_DATA' => 'ASSIGN',

        // Conditionals
        'PIVOT_TO_VIDEO' => 'IF',
        'PIVOT_TO_REELS' => 'ELSEIF',
        'PIVOT_TO_METAVERSE' => 'ELSE',
        'END_PIVOT' => 'ENDIF',

        // Loops
        'MOVE_FAST' => 'WHILE',
        'BREAK_THINGS' => 'ENDWHILE',
        'GROWTH_HACK' => 'FOR',
        'PLATEAU' => 'ENDFOR',
        'HARVEST_USERS' => 'FOREACH',
        'USERS_HARVESTED' => 'ENDFOREACH',
        'AS' => 'AS',

        // Functions
        'FEATURE' => 'FUNCTION',
        'IPO' => 'RETURN',

        // Classes
        'CORPORATION' => 'CLASS',
        'ACQUIRE' => 'NEW',
        'OPEN_GRAPH' => 'PUBLIC',
        'SHADOW_PROFILE' => 'PRIVATE',
        'FRIENDS_ONLY' => 'PROTECTED',
        'ACQUIRES' => 'EXTENDS',
        'COPIES' => 'IMPLEMENTS',
        'REGULATION' => 'INTERFACE',
        'METAVERSE_CONCEPT' => 'ABSTRACT',
        'DATACENTER' => 'STATIC',
        'THE_ZUCC' => 'THIS',
        'FACEBOOK_PROPER' => 'SELF',
        'HARVARD_DROPOUT' => 'PARENT',

        // Boolean/Null
        'CONNECTED' => 'TRUE',
        'DISCONNECTED' => 'FALSE',
        'MYSPACE' => 'NULL',

        // Error handling
        'CONGRESSIONAL_HEARING' => 'TRY',
        'TAKE_RESPONSIBILITY' => 'CATCH',
        'BLAME_RUSSIA' => 'THROW',

        // Other
        'IMMUTABLE_LIKE_MY_HAIR' => 'CONST',
        'RAGE_QUIT' => 'BREAK',
        'SCROLL_PAST' => 'CONTINUE',
        'A_B_TEST' => 'SWITCH',
        'VARIANT' => 'CASE',
        'CONTROL_GROUP' => 'DEFAULT',
        'SHUTDOWN_LIKE_VINE' => 'EXIT',
        'WORLDWIDE_EXCEPT_CHINA' => 'GLOBAL',
        'ACQUIRE_TALENT' => 'REQUIRE',
        'COPY_FROM_SNAPCHAT' => 'INCLUDE',
        'REBRAND_TO' => 'NAMESPACE',
        'INTEGRATE' => 'USE',

        // Type hints
        'SOCIAL_GRAPH' => 'ARRAY',
        'STATUS_UPDATE' => 'STRING_TYPE',
        'DAILY_ACTIVE_USERS' => 'INT_TYPE',
        'STOCK_PRICE' => 'FLOAT_TYPE',
        'FACT_CHECK' => 'BOOL_TYPE',

        // Built-in functions
        'COLLECT' => 'BUILTIN_INPUT',
        'MONETIZE' => 'BUILTIN_TOSTRING',
        'COUNT_USERS' => 'BUILTIN_COUNT',
        'BOOST' => 'BUILTIN_BOOST',
        'ALGORITHM' => 'BUILTIN_SORT',
        'SHADOWBAN' => 'BUILTIN_UNSET',
        'FACT_CHECK_THIS' => 'BUILTIN_BOOL',
        'TIME_ON_PLATFORM' => 'BUILTIN_TIME',
        'RANDOM_AD' => 'BUILTIN_RAND',

        // Magic constants
        'ZUCKS_AGE' => 'MAGIC_BIRTHYEAR',
        'SWEET_BABY_RAYS' => 'MAGIC_BBQ',
        'LIZARD_PERSON' => 'MAGIC_SELF',
    ];

    // Multi-character operators
    private array $operators = [
        'IS_CONNECTED_TO' => 'EQ',
        'UNFRIENDED' => 'NEQ',
        'AND_ALSO_YOUR_DATA' => 'AND',
        'OR_YOUR_FRIENDS_DATA' => 'OR',
        'FAKE_NEWS' => 'NOT',
        'ENGAGEMENT' => 'INCREMENT',
        'CHURN' => 'DECREMENT',
        'MERGE' => 'PLUS',
        'DIVEST' => 'MINUS',
        'SCALE' => 'MULTIPLY',
        'SPLIT' => 'DIVIDE',
        'REMAINDER_OF_PRIVACY' => 'MODULO',
        '=>' => 'DOUBLE_ARROW',
        '->' => 'ARROW',
        '::' => 'DOUBLE_COLON',
        '<=' => 'LTE',
        '>=' => 'GTE',
        '==' => 'EQ_SIMPLE',
        '!=' => 'NEQ_SIMPLE',
        '&&' => 'AND_SIMPLE',
        '||' => 'OR_SIMPLE',
        '++' => 'INC_SIMPLE',
        '--' => 'DEC_SIMPLE',
    ];

    public function __construct(string $source)
    {
        $this->source = $source;
        $this->length = strlen($source);
    }

    public function tokenize(): array
    {
        $tokens = [];

        // Check for opening tag
        $this->skipWhitespace();
        if (!$this->match('<?zuck')) {
            throw new \Exception("Zuck# programs must start with <?zuck");
        }
        $tokens[] = new Token('OPEN_TAG', '<?zuck', $this->line, $this->column);

        while ($this->pos < $this->length) {
            $this->skipWhitespace();

            if ($this->pos >= $this->length) {
                break;
            }

            // Check for closing tag
            if ($this->match('?>')) {
                $tokens[] = new Token('CLOSE_TAG', '?>', $this->line, $this->column);
                break;
            }

            // Skip comments
            if ($this->match('REDACTED')) {
                $this->skipLineComment();
                continue;
            }

            if ($this->match('TERMS_OF_SERVICE')) {
                $this->skipBlockComment();
                continue;
            }

            // Also support // and /* */ style comments
            if ($this->peek() === '/' && $this->peekNext() === '/') {
                $this->advance();
                $this->advance();
                $this->skipLineComment();
                continue;
            }

            if ($this->peek() === '/' && $this->peekNext() === '*') {
                $this->advance();
                $this->advance();
                $this->skipBlockCommentSimple();
                continue;
            }

            $token = $this->nextToken();
            if ($token !== null) {
                $tokens[] = $token;
            }
        }

        $tokens[] = new Token('EOF', null, $this->line, $this->column);
        return $tokens;
    }

    private function nextToken(): ?Token
    {
        $char = $this->peek();
        $line = $this->line;
        $column = $this->column;

        // String literals
        if ($char === '"' || $char === "'") {
            return $this->readString($char);
        }

        // Numbers
        if (is_numeric($char) || ($char === '-' && is_numeric($this->peekNext()))) {
            return $this->readNumber();
        }

        // Variables (start with $)
        if ($char === '$') {
            return $this->readVariable();
        }

        // Check for multi-character operators first
        foreach ($this->operators as $op => $type) {
            if ($this->match($op)) {
                return new Token($type, $op, $line, $column);
            }
        }

        // Single character tokens
        $singleChars = [
            '(' => 'LPAREN',
            ')' => 'RPAREN',
            '{' => 'LBRACE',
            '}' => 'RBRACE',
            '[' => 'LBRACKET',
            ']' => 'RBRACKET',
            ';' => 'SEMICOLON',
            ',' => 'COMMA',
            '.' => 'DOT',
            '+' => 'PLUS',
            '-' => 'MINUS',
            '*' => 'MULTIPLY',
            '/' => 'DIVIDE',
            '%' => 'MODULO',
            '=' => 'ASSIGN_OP',
            '<' => 'LT',
            '>' => 'GT',
            '!' => 'NOT_SIMPLE',
            ':' => 'COLON',
            '?' => 'QUESTION',
        ];

        if (isset($singleChars[$char])) {
            $this->advance();
            return new Token($singleChars[$char], $char, $line, $column);
        }

        // Keywords and identifiers
        if (ctype_alpha($char) || $char === '_') {
            return $this->readIdentifier();
        }

        throw new \Exception("Unexpected character '{$char}' at line {$line}, column {$column}");
    }

    private function readString(string $quote): Token
    {
        $line = $this->line;
        $column = $this->column;
        $this->advance(); // Skip opening quote

        $value = '';
        while ($this->pos < $this->length && $this->peek() !== $quote) {
            if ($this->peek() === '\\') {
                $this->advance();
                $escaped = $this->peek();
                $value .= match ($escaped) {
                    'n' => "\n",
                    't' => "\t",
                    'r' => "\r",
                    '\\' => "\\",
                    '"' => '"',
                    "'" => "'",
                    default => $escaped,
                };
            } else {
                $value .= $this->peek();
            }
            $this->advance();
        }

        if ($this->pos >= $this->length) {
            throw new \Exception("Unterminated string at line {$line}");
        }

        $this->advance(); // Skip closing quote
        return new Token('STRING', $value, $line, $column);
    }

    private function readNumber(): Token
    {
        $line = $this->line;
        $column = $this->column;
        $value = '';

        if ($this->peek() === '-') {
            $value .= $this->peek();
            $this->advance();
        }

        while ($this->pos < $this->length && (is_numeric($this->peek()) || $this->peek() === '.')) {
            $value .= $this->peek();
            $this->advance();
        }

        $type = str_contains($value, '.') ? 'FLOAT' : 'INTEGER';
        return new Token($type, $type === 'FLOAT' ? (float)$value : (int)$value, $line, $column);
    }

    private function readVariable(): Token
    {
        $line = $this->line;
        $column = $this->column;
        $this->advance(); // Skip $

        $name = '';
        while ($this->pos < $this->length && (ctype_alnum($this->peek()) || $this->peek() === '_')) {
            $name .= $this->peek();
            $this->advance();
        }

        return new Token('VARIABLE', '$' . $name, $line, $column);
    }

    private function readIdentifier(): Token
    {
        $line = $this->line;
        $column = $this->column;
        $value = '';

        while ($this->pos < $this->length && (ctype_alnum($this->peek()) || $this->peek() === '_')) {
            $value .= $this->peek();
            $this->advance();
        }

        // Check if it's a keyword
        if (isset($this->keywords[$value])) {
            return new Token($this->keywords[$value], $value, $line, $column);
        }

        // Check if it's an operator keyword
        if (isset($this->operators[$value])) {
            return new Token($this->operators[$value], $value, $line, $column);
        }

        return new Token('IDENTIFIER', $value, $line, $column);
    }

    private function skipWhitespace(): void
    {
        while ($this->pos < $this->length && ctype_space($this->peek())) {
            if ($this->peek() === "\n") {
                $this->line++;
                $this->column = 0;
            }
            $this->advance();
        }
    }

    private function skipLineComment(): void
    {
        while ($this->pos < $this->length && $this->peek() !== "\n") {
            $this->advance();
        }
    }

    private function skipBlockComment(): void
    {
        while ($this->pos < $this->length && !$this->match('END_TOS')) {
            if ($this->peek() === "\n") {
                $this->line++;
                $this->column = 0;
            }
            $this->advance();
        }
    }

    private function skipBlockCommentSimple(): void
    {
        while ($this->pos < $this->length) {
            if ($this->peek() === '*' && $this->peekNext() === '/') {
                $this->advance();
                $this->advance();
                return;
            }
            if ($this->peek() === "\n") {
                $this->line++;
                $this->column = 0;
            }
            $this->advance();
        }
    }

    private function peek(): string
    {
        return $this->source[$this->pos] ?? '';
    }

    private function peekNext(): string
    {
        return $this->source[$this->pos + 1] ?? '';
    }

    private function advance(): void
    {
        $this->pos++;
        $this->column++;
    }

    private function match(string $expected): bool
    {
        $len = strlen($expected);
        if (substr($this->source, $this->pos, $len) === $expected) {
            // Make sure it's not part of a longer identifier
            $nextChar = $this->source[$this->pos + $len] ?? '';
            if (ctype_alnum($expected[0]) && (ctype_alnum($nextChar) || $nextChar === '_')) {
                return false;
            }
            $this->pos += $len;
            $this->column += $len;
            return true;
        }
        return false;
    }
}
