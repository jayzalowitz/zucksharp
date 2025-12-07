<?php

declare(strict_types=1);

namespace ZuckSharp;

// AST Node classes
abstract class ASTNode {}

class Program extends ASTNode
{
    public function __construct(public array $statements) {}
}

class EchoStatement extends ASTNode
{
    public function __construct(public ASTNode $expression) {}
}

class PrintStatement extends ASTNode
{
    public function __construct(public ASTNode $expression) {}
}

class AssignmentStatement extends ASTNode
{
    public function __construct(
        public string $variable,
        public ASTNode $value
    ) {}
}

class VariableExpression extends ASTNode
{
    public function __construct(public string $name) {}
}

class LiteralExpression extends ASTNode
{
    public function __construct(public mixed $value, public string $type) {}
}

class BinaryExpression extends ASTNode
{
    public function __construct(
        public ASTNode $left,
        public string $operator,
        public ASTNode $right
    ) {}
}

class UnaryExpression extends ASTNode
{
    public function __construct(
        public string $operator,
        public ASTNode $operand
    ) {}
}

class IfStatement extends ASTNode
{
    public function __construct(
        public ASTNode $condition,
        public array $thenBranch,
        public array $elseIfBranches = [],
        public ?array $elseBranch = null
    ) {}
}

class WhileStatement extends ASTNode
{
    public function __construct(
        public ASTNode $condition,
        public array $body
    ) {}
}

class ForStatement extends ASTNode
{
    public function __construct(
        public ?ASTNode $init,
        public ?ASTNode $condition,
        public ?ASTNode $increment,
        public array $body
    ) {}
}

class ForeachStatement extends ASTNode
{
    public function __construct(
        public ASTNode $iterable,
        public ?string $keyVar,
        public string $valueVar,
        public array $body
    ) {}
}

class FunctionDeclaration extends ASTNode
{
    public function __construct(
        public string $name,
        public array $params,
        public array $body,
        public ?string $visibility = null
    ) {}
}

class FunctionCall extends ASTNode
{
    public function __construct(
        public string $name,
        public array $arguments
    ) {}
}

class ReturnStatement extends ASTNode
{
    public function __construct(public ?ASTNode $value = null) {}
}

class ClassDeclaration extends ASTNode
{
    public function __construct(
        public string $name,
        public array $members,
        public ?string $extends = null,
        public array $implements = []
    ) {}
}

class PropertyDeclaration extends ASTNode
{
    public function __construct(
        public string $visibility,
        public string $name,
        public ?ASTNode $defaultValue = null,
        public bool $isStatic = false
    ) {}
}

class MethodDeclaration extends ASTNode
{
    public function __construct(
        public string $visibility,
        public string $name,
        public array $params,
        public array $body,
        public bool $isStatic = false
    ) {}
}

class NewExpression extends ASTNode
{
    public function __construct(
        public string $className,
        public array $arguments
    ) {}
}

class PropertyAccess extends ASTNode
{
    public function __construct(
        public ASTNode $object,
        public string $property
    ) {}
}

class MethodCall extends ASTNode
{
    public function __construct(
        public ASTNode $object,
        public string $method,
        public array $arguments
    ) {}
}

class ArrayExpression extends ASTNode
{
    public function __construct(public array $elements) {}
}

class ArrayAccess extends ASTNode
{
    public function __construct(
        public ASTNode $array,
        public ASTNode $index
    ) {}
}

class TryCatchStatement extends ASTNode
{
    public function __construct(
        public array $tryBlock,
        public string $exceptionVar,
        public ?string $exceptionType,
        public array $catchBlock
    ) {}
}

class ThrowStatement extends ASTNode
{
    public function __construct(public ASTNode $expression) {}
}

class BreakStatement extends ASTNode {}
class ContinueStatement extends ASTNode {}

class IncrementExpression extends ASTNode
{
    public function __construct(
        public string $variable,
        public bool $isPrefix = false
    ) {}
}

class DecrementExpression extends ASTNode
{
    public function __construct(
        public string $variable,
        public bool $isPrefix = false
    ) {}
}

class ExitStatement extends ASTNode
{
    public function __construct(public ?ASTNode $code = null) {}
}

class Parser
{
    private array $tokens;
    private int $pos = 0;
    private int $length;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->length = count($tokens);
    }

    public function parse(): Program
    {
        $this->expect('OPEN_TAG');
        $statements = [];

        while (!$this->check('CLOSE_TAG') && !$this->check('EOF')) {
            $stmt = $this->parseStatement();
            if ($stmt !== null) {
                $statements[] = $stmt;
            }
        }

        if ($this->check('CLOSE_TAG')) {
            $this->advance();
        }

        return new Program($statements);
    }

    private function parseStatement(): ?ASTNode
    {
        $token = $this->current();

        return match ($token->type) {
            'ECHO' => $this->parseEchoStatement(),
            'PRINT' => $this->parsePrintStatement(),
            'ASSIGN' => $this->parseAssignmentStatement(),
            'VARIABLE' => $this->parseVariableStatement(),
            'IF' => $this->parseIfStatement(),
            'WHILE' => $this->parseWhileStatement(),
            'FOR' => $this->parseForStatement(),
            'FOREACH' => $this->parseForeachStatement(),
            'FUNCTION' => $this->parseFunctionDeclaration(),
            'RETURN' => $this->parseReturnStatement(),
            'CLASS' => $this->parseClassDeclaration(),
            'TRY' => $this->parseTryCatchStatement(),
            'THROW' => $this->parseThrowStatement(),
            'BREAK' => $this->parseBreakStatement(),
            'CONTINUE' => $this->parseContinueStatement(),
            'EXIT' => $this->parseExitStatement(),
            'PUBLIC', 'PRIVATE', 'PROTECTED' => $this->parseVisibilityStatement(),
            default => $this->parseExpressionStatement(),
        };
    }

    private function parseEchoStatement(): EchoStatement
    {
        $this->advance(); // Skip ECHO
        $expr = $this->parseExpression();
        $this->expect('SEMICOLON');
        return new EchoStatement($expr);
    }

    private function parsePrintStatement(): PrintStatement
    {
        $this->advance(); // Skip PRINT
        $expr = $this->parseExpression();
        $this->expect('SEMICOLON');
        return new PrintStatement($expr);
    }

    private function parseAssignmentStatement(): AssignmentStatement
    {
        $this->advance(); // Skip ASSIGN (STEAL_DATA)
        $varToken = $this->expect('VARIABLE');
        $this->expect('ASSIGN_OP');
        $value = $this->parseExpression();
        $this->expect('SEMICOLON');
        return new AssignmentStatement($varToken->value, $value);
    }

    private function parseVariableStatement(): ASTNode
    {
        $varToken = $this->current();
        $this->advance();

        // Check for increment/decrement
        if ($this->check('INCREMENT') || $this->check('INC_SIMPLE')) {
            $this->advance();
            $this->expect('SEMICOLON');
            return new IncrementExpression($varToken->value);
        }

        if ($this->check('DECREMENT') || $this->check('DEC_SIMPLE')) {
            $this->advance();
            $this->expect('SEMICOLON');
            return new DecrementExpression($varToken->value);
        }

        // Check for assignment
        if ($this->check('ASSIGN_OP')) {
            $this->advance();
            $value = $this->parseExpression();
            $this->expect('SEMICOLON');
            return new AssignmentStatement($varToken->value, $value);
        }

        // Check for method call or property access
        if ($this->check('ARROW')) {
            $this->pos--; // Go back to reparse as expression
            $expr = $this->parseExpression();
            $this->expect('SEMICOLON');
            return $expr;
        }

        // Check for array access assignment
        if ($this->check('LBRACKET')) {
            $this->pos--; // Go back to reparse as expression
            $expr = $this->parseExpression();

            if ($this->check('ASSIGN_OP')) {
                $this->advance();
                $value = $this->parseExpression();
                $this->expect('SEMICOLON');
                // This is an array assignment - we need to handle this differently
                return new AssignmentStatement($varToken->value . '_array_access', $value);
            }

            $this->expect('SEMICOLON');
            return $expr;
        }

        throw new \Exception("Unexpected token after variable at line {$varToken->line}");
    }

    private function parseIfStatement(): IfStatement
    {
        $this->advance(); // Skip IF
        $this->expect('LPAREN');
        $condition = $this->parseExpression();
        $this->expect('RPAREN');
        $this->expect('LBRACE');

        $thenBranch = [];
        while (!$this->check('RBRACE')) {
            $thenBranch[] = $this->parseStatement();
        }
        $this->expect('RBRACE');

        $elseIfBranches = [];
        while ($this->check('ELSEIF')) {
            $this->advance();
            $this->expect('LPAREN');
            $elseIfCondition = $this->parseExpression();
            $this->expect('RPAREN');
            $this->expect('LBRACE');

            $elseIfBody = [];
            while (!$this->check('RBRACE')) {
                $elseIfBody[] = $this->parseStatement();
            }
            $this->expect('RBRACE');

            $elseIfBranches[] = ['condition' => $elseIfCondition, 'body' => $elseIfBody];
        }

        $elseBranch = null;
        if ($this->check('ELSE')) {
            $this->advance();
            $this->expect('LBRACE');
            $elseBranch = [];
            while (!$this->check('RBRACE')) {
                $elseBranch[] = $this->parseStatement();
            }
            $this->expect('RBRACE');
        }

        return new IfStatement($condition, $thenBranch, $elseIfBranches, $elseBranch);
    }

    private function parseWhileStatement(): WhileStatement
    {
        $this->advance(); // Skip WHILE
        $this->expect('LPAREN');
        $condition = $this->parseExpression();
        $this->expect('RPAREN');
        $this->expect('LBRACE');

        $body = [];
        while (!$this->check('RBRACE')) {
            $body[] = $this->parseStatement();
        }
        $this->expect('RBRACE');

        // Optional ENDWHILE keyword
        if ($this->check('ENDWHILE')) {
            $this->advance();
        }

        return new WhileStatement($condition, $body);
    }

    private function parseForStatement(): ForStatement
    {
        $this->advance(); // Skip FOR
        $this->expect('LPAREN');

        $init = null;
        if (!$this->check('SEMICOLON')) {
            if ($this->check('ASSIGN')) {
                $this->advance();
            }
            if ($this->check('VARIABLE')) {
                $varToken = $this->current();
                $this->advance();
                $this->expect('ASSIGN_OP');
                $value = $this->parseExpression();
                $init = new AssignmentStatement($varToken->value, $value);
            }
        }
        $this->expect('SEMICOLON');

        $condition = null;
        if (!$this->check('SEMICOLON')) {
            $condition = $this->parseExpression();
        }
        $this->expect('SEMICOLON');

        $increment = null;
        if (!$this->check('RPAREN')) {
            $increment = $this->parseExpression();
        }
        $this->expect('RPAREN');
        $this->expect('LBRACE');

        $body = [];
        while (!$this->check('RBRACE')) {
            $body[] = $this->parseStatement();
        }
        $this->expect('RBRACE');

        return new ForStatement($init, $condition, $increment, $body);
    }

    private function parseForeachStatement(): ForeachStatement
    {
        $this->advance(); // Skip FOREACH
        $this->expect('LPAREN');
        $iterable = $this->parseExpression();
        $this->expect('AS');

        $keyVar = null;
        $valueVar = $this->expect('VARIABLE')->value;

        if ($this->check('DOUBLE_ARROW')) {
            $this->advance();
            $keyVar = $valueVar;
            $valueVar = $this->expect('VARIABLE')->value;
        }

        $this->expect('RPAREN');
        $this->expect('LBRACE');

        $body = [];
        while (!$this->check('RBRACE')) {
            $body[] = $this->parseStatement();
        }
        $this->expect('RBRACE');

        return new ForeachStatement($iterable, $keyVar, $valueVar, $body);
    }

    private function parseFunctionDeclaration(?string $visibility = null): FunctionDeclaration
    {
        $this->advance(); // Skip FUNCTION
        $name = $this->expect('IDENTIFIER')->value;
        $this->expect('LPAREN');

        $params = [];
        if (!$this->check('RPAREN')) {
            do {
                $params[] = $this->expect('VARIABLE')->value;
            } while ($this->match('COMMA'));
        }
        $this->expect('RPAREN');
        $this->expect('LBRACE');

        $body = [];
        while (!$this->check('RBRACE')) {
            $body[] = $this->parseStatement();
        }
        $this->expect('RBRACE');

        return new FunctionDeclaration($name, $params, $body, $visibility);
    }

    private function parseReturnStatement(): ReturnStatement
    {
        $this->advance(); // Skip RETURN
        $value = null;
        if (!$this->check('SEMICOLON')) {
            $value = $this->parseExpression();
        }
        $this->expect('SEMICOLON');
        return new ReturnStatement($value);
    }

    private function parseClassDeclaration(): ClassDeclaration
    {
        $this->advance(); // Skip CLASS
        $name = $this->expect('IDENTIFIER')->value;

        $extends = null;
        if ($this->check('EXTENDS')) {
            $this->advance();
            $extends = $this->expect('IDENTIFIER')->value;
        }

        $implements = [];
        if ($this->check('IMPLEMENTS')) {
            $this->advance();
            do {
                $implements[] = $this->expect('IDENTIFIER')->value;
            } while ($this->match('COMMA'));
        }

        $this->expect('LBRACE');

        $members = [];
        while (!$this->check('RBRACE')) {
            $members[] = $this->parseClassMember();
        }
        $this->expect('RBRACE');

        return new ClassDeclaration($name, $members, $extends, $implements);
    }

    private function parseClassMember(): ASTNode
    {
        $visibility = 'public';
        $isStatic = false;

        if ($this->check('PUBLIC') || $this->check('PRIVATE') || $this->check('PROTECTED')) {
            $visibility = strtolower($this->current()->type);
            $this->advance();
        }

        if ($this->check('STATIC')) {
            $isStatic = true;
            $this->advance();
        }

        if ($this->check('FUNCTION')) {
            $this->advance();
            $name = $this->expect('IDENTIFIER')->value;
            $this->expect('LPAREN');

            $params = [];
            if (!$this->check('RPAREN')) {
                do {
                    $params[] = $this->expect('VARIABLE')->value;
                } while ($this->match('COMMA'));
            }
            $this->expect('RPAREN');
            $this->expect('LBRACE');

            $body = [];
            while (!$this->check('RBRACE')) {
                $body[] = $this->parseStatement();
            }
            $this->expect('RBRACE');

            return new MethodDeclaration($visibility, $name, $params, $body, $isStatic);
        }

        // Property
        $varToken = $this->expect('VARIABLE');
        $defaultValue = null;
        if ($this->check('ASSIGN_OP')) {
            $this->advance();
            $defaultValue = $this->parseExpression();
        }
        $this->expect('SEMICOLON');

        return new PropertyDeclaration($visibility, $varToken->value, $defaultValue, $isStatic);
    }

    private function parseTryCatchStatement(): TryCatchStatement
    {
        $this->advance(); // Skip TRY
        $this->expect('LBRACE');

        $tryBlock = [];
        while (!$this->check('RBRACE')) {
            $tryBlock[] = $this->parseStatement();
        }
        $this->expect('RBRACE');

        $this->expect('CATCH');
        $this->expect('LPAREN');

        $exceptionType = null;
        if ($this->check('IDENTIFIER')) {
            $exceptionType = $this->current()->value;
            $this->advance();
        }

        $exceptionVar = $this->expect('VARIABLE')->value;
        $this->expect('RPAREN');
        $this->expect('LBRACE');

        $catchBlock = [];
        while (!$this->check('RBRACE')) {
            $catchBlock[] = $this->parseStatement();
        }
        $this->expect('RBRACE');

        return new TryCatchStatement($tryBlock, $exceptionVar, $exceptionType, $catchBlock);
    }

    private function parseThrowStatement(): ThrowStatement
    {
        $this->advance(); // Skip THROW
        $expr = $this->parseExpression();
        $this->expect('SEMICOLON');
        return new ThrowStatement($expr);
    }

    private function parseBreakStatement(): BreakStatement
    {
        $this->advance();
        $this->expect('SEMICOLON');
        return new BreakStatement();
    }

    private function parseContinueStatement(): ContinueStatement
    {
        $this->advance();
        $this->expect('SEMICOLON');
        return new ContinueStatement();
    }

    private function parseExitStatement(): ExitStatement
    {
        $this->advance();
        $code = null;
        if ($this->check('LPAREN')) {
            $this->advance();
            if (!$this->check('RPAREN')) {
                $code = $this->parseExpression();
            }
            $this->expect('RPAREN');
        }
        $this->expect('SEMICOLON');
        return new ExitStatement($code);
    }

    private function parseVisibilityStatement(): ASTNode
    {
        $visibility = strtolower($this->current()->type);
        $this->advance();

        if ($this->check('FUNCTION')) {
            return $this->parseFunctionDeclaration($visibility);
        }

        throw new \Exception("Expected function after visibility modifier");
    }

    private function parseExpressionStatement(): ?ASTNode
    {
        if ($this->check('SEMICOLON')) {
            $this->advance();
            return null;
        }

        $expr = $this->parseExpression();
        $this->expect('SEMICOLON');
        return $expr;
    }

    private function parseExpression(): ASTNode
    {
        return $this->parseOr();
    }

    private function parseOr(): ASTNode
    {
        $left = $this->parseAnd();

        while ($this->check('OR') || $this->check('OR_SIMPLE')) {
            $op = $this->current()->value;
            $this->advance();
            $right = $this->parseAnd();
            $left = new BinaryExpression($left, '||', $right);
        }

        return $left;
    }

    private function parseAnd(): ASTNode
    {
        $left = $this->parseEquality();

        while ($this->check('AND') || $this->check('AND_SIMPLE')) {
            $op = $this->current()->value;
            $this->advance();
            $right = $this->parseEquality();
            $left = new BinaryExpression($left, '&&', $right);
        }

        return $left;
    }

    private function parseEquality(): ASTNode
    {
        $left = $this->parseComparison();

        while ($this->check('EQ') || $this->check('NEQ') || $this->check('EQ_SIMPLE') || $this->check('NEQ_SIMPLE')) {
            $op = match ($this->current()->type) {
                'EQ', 'EQ_SIMPLE' => '==',
                'NEQ', 'NEQ_SIMPLE' => '!=',
            };
            $this->advance();
            $right = $this->parseComparison();
            $left = new BinaryExpression($left, $op, $right);
        }

        return $left;
    }

    private function parseComparison(): ASTNode
    {
        $left = $this->parseTerm();

        while ($this->check('LT') || $this->check('GT') || $this->check('LTE') || $this->check('GTE')) {
            $op = match ($this->current()->type) {
                'LT' => '<',
                'GT' => '>',
                'LTE' => '<=',
                'GTE' => '>=',
            };
            $this->advance();
            $right = $this->parseTerm();
            $left = new BinaryExpression($left, $op, $right);
        }

        return $left;
    }

    private function parseTerm(): ASTNode
    {
        $left = $this->parseFactor();

        while ($this->check('PLUS') || $this->check('MINUS') || $this->check('DOT')) {
            $op = match ($this->current()->type) {
                'PLUS' => '+',
                'MINUS' => '-',
                'DOT' => '.',
            };
            $this->advance();
            $right = $this->parseFactor();
            $left = new BinaryExpression($left, $op, $right);
        }

        return $left;
    }

    private function parseFactor(): ASTNode
    {
        $left = $this->parseUnary();

        while ($this->check('MULTIPLY') || $this->check('DIVIDE') || $this->check('MODULO')) {
            $op = match ($this->current()->type) {
                'MULTIPLY' => '*',
                'DIVIDE' => '/',
                'MODULO' => '%',
            };
            $this->advance();
            $right = $this->parseUnary();
            $left = new BinaryExpression($left, $op, $right);
        }

        return $left;
    }

    private function parseUnary(): ASTNode
    {
        if ($this->check('NOT') || $this->check('NOT_SIMPLE')) {
            $this->advance();
            $operand = $this->parseUnary();
            return new UnaryExpression('!', $operand);
        }

        if ($this->check('MINUS')) {
            $this->advance();
            $operand = $this->parseUnary();
            return new UnaryExpression('-', $operand);
        }

        if ($this->check('INCREMENT') || $this->check('INC_SIMPLE')) {
            $this->advance();
            $var = $this->expect('VARIABLE');
            return new IncrementExpression($var->value, true);
        }

        if ($this->check('DECREMENT') || $this->check('DEC_SIMPLE')) {
            $this->advance();
            $var = $this->expect('VARIABLE');
            return new DecrementExpression($var->value, true);
        }

        return $this->parsePostfix();
    }

    private function parsePostfix(): ASTNode
    {
        $expr = $this->parsePrimary();

        while (true) {
            if ($this->check('INCREMENT') || $this->check('INC_SIMPLE')) {
                $this->advance();
                if ($expr instanceof VariableExpression) {
                    $expr = new IncrementExpression($expr->name);
                }
            } elseif ($this->check('DECREMENT') || $this->check('DEC_SIMPLE')) {
                $this->advance();
                if ($expr instanceof VariableExpression) {
                    $expr = new DecrementExpression($expr->name);
                }
            } elseif ($this->check('LBRACKET')) {
                $this->advance();
                $index = $this->parseExpression();
                $this->expect('RBRACKET');
                $expr = new ArrayAccess($expr, $index);
            } elseif ($this->check('ARROW')) {
                $this->advance();
                $member = $this->expect('IDENTIFIER', 'VARIABLE');

                if ($this->check('LPAREN')) {
                    $this->advance();
                    $args = [];
                    if (!$this->check('RPAREN')) {
                        do {
                            $args[] = $this->parseExpression();
                        } while ($this->match('COMMA'));
                    }
                    $this->expect('RPAREN');
                    $expr = new MethodCall($expr, ltrim($member->value, '$'), $args);
                } else {
                    $expr = new PropertyAccess($expr, ltrim($member->value, '$'));
                }
            } elseif ($this->check('LPAREN') && $expr instanceof VariableExpression) {
                // This shouldn't happen for variables, skip
                break;
            } else {
                break;
            }
        }

        return $expr;
    }

    private function parsePrimary(): ASTNode
    {
        $token = $this->current();

        // Literals
        if ($token->type === 'STRING') {
            $this->advance();
            return new LiteralExpression($token->value, 'string');
        }

        if ($token->type === 'INTEGER') {
            $this->advance();
            return new LiteralExpression($token->value, 'int');
        }

        if ($token->type === 'FLOAT') {
            $this->advance();
            return new LiteralExpression($token->value, 'float');
        }

        if ($token->type === 'TRUE') {
            $this->advance();
            return new LiteralExpression(true, 'bool');
        }

        if ($token->type === 'FALSE') {
            $this->advance();
            return new LiteralExpression(false, 'bool');
        }

        if ($token->type === 'NULL') {
            $this->advance();
            return new LiteralExpression(null, 'null');
        }

        // Magic constants
        if ($token->type === 'MAGIC_BIRTHYEAR') {
            $this->advance();
            return new LiteralExpression(2000, 'int');
        }

        if ($token->type === 'MAGIC_BBQ') {
            $this->advance();
            return new LiteralExpression('BBQ', 'string');
        }

        // Variables
        if ($token->type === 'VARIABLE') {
            $this->advance();
            return new VariableExpression($token->value);
        }

        // THIS reference
        if ($token->type === 'THIS') {
            $this->advance();
            return new VariableExpression('$this');
        }

        // Array literal
        if ($token->type === 'ARRAY' || $token->type === 'LBRACKET') {
            return $this->parseArrayLiteral();
        }

        // New object
        if ($token->type === 'NEW') {
            $this->advance();
            $className = $this->expect('IDENTIFIER')->value;
            $this->expect('LPAREN');
            $args = [];
            if (!$this->check('RPAREN')) {
                do {
                    $args[] = $this->parseExpression();
                } while ($this->match('COMMA'));
            }
            $this->expect('RPAREN');
            return new NewExpression($className, $args);
        }

        // Parenthesized expression
        if ($token->type === 'LPAREN') {
            $this->advance();
            $expr = $this->parseExpression();
            $this->expect('RPAREN');
            return $expr;
        }

        // Function call or identifier
        if ($token->type === 'IDENTIFIER' || str_starts_with($token->type, 'BUILTIN_')) {
            $name = $token->value;
            $this->advance();

            if ($this->check('LPAREN')) {
                $this->advance();
                $args = [];
                if (!$this->check('RPAREN')) {
                    do {
                        $args[] = $this->parseExpression();
                    } while ($this->match('COMMA'));
                }
                $this->expect('RPAREN');
                return new FunctionCall($name, $args);
            }

            return new VariableExpression($name);
        }

        throw new \Exception("Unexpected token {$token->type} at line {$token->line}");
    }

    private function parseArrayLiteral(): ArrayExpression
    {
        if ($this->check('ARRAY')) {
            $this->advance();
        }

        $useBracket = $this->check('LBRACKET');
        $this->expect($useBracket ? 'LBRACKET' : 'LPAREN');

        $elements = [];
        if (!$this->check($useBracket ? 'RBRACKET' : 'RPAREN')) {
            do {
                $key = null;
                $value = $this->parseExpression();

                if ($this->check('DOUBLE_ARROW')) {
                    $this->advance();
                    $key = $value;
                    $value = $this->parseExpression();
                }

                $elements[] = ['key' => $key, 'value' => $value];
            } while ($this->match('COMMA'));
        }

        $this->expect($useBracket ? 'RBRACKET' : 'RPAREN');
        return new ArrayExpression($elements);
    }

    private function current(): Token
    {
        return $this->tokens[$this->pos];
    }

    private function advance(): Token
    {
        $token = $this->current();
        $this->pos++;
        return $token;
    }

    private function check(string $type): bool
    {
        return $this->current()->type === $type;
    }

    private function match(string $type): bool
    {
        if ($this->check($type)) {
            $this->advance();
            return true;
        }
        return false;
    }

    private function expect(string ...$types): Token
    {
        foreach ($types as $type) {
            if ($this->check($type)) {
                return $this->advance();
            }
        }
        $token = $this->current();
        $expected = implode(' or ', $types);
        throw new \Exception("Expected {$expected}, got {$token->type} at line {$token->line}");
    }
}
