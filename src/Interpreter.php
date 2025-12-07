<?php

declare(strict_types=1);

namespace ZuckSharp;

class ReturnValue extends \Exception
{
    public function __construct(public mixed $value) {
        parent::__construct();
    }
}

class BreakException extends \Exception {}
class ContinueException extends \Exception {}

class ZuckObject
{
    public string $className;
    public array $properties = [];
    public array $methods = [];

    public function __construct(string $className)
    {
        $this->className = $className;
    }
}

class Environment
{
    private array $variables = [];
    private ?Environment $parent;

    public function __construct(?Environment $parent = null)
    {
        $this->parent = $parent;
    }

    public function define(string $name, mixed $value): void
    {
        $this->variables[$name] = $value;
    }

    public function get(string $name): mixed
    {
        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        if ($this->parent !== null) {
            return $this->parent->get($name);
        }

        throw new \Exception("Undefined variable: {$name}");
    }

    public function set(string $name, mixed $value): void
    {
        if (array_key_exists($name, $this->variables)) {
            $this->variables[$name] = $value;
            return;
        }

        if ($this->parent !== null) {
            $this->parent->set($name, $value);
            return;
        }

        // Define in current scope if not found
        $this->variables[$name] = $value;
    }

    public function has(string $name): bool
    {
        if (array_key_exists($name, $this->variables)) {
            return true;
        }
        return $this->parent?->has($name) ?? false;
    }
}

class Interpreter
{
    private Environment $globalEnv;
    private Environment $currentEnv;
    private array $functions = [];
    private array $classes = [];
    private array $output = [];

    public function __construct()
    {
        $this->globalEnv = new Environment();
        $this->currentEnv = $this->globalEnv;
    }

    public function interpret(Program $program): string
    {
        foreach ($program->statements as $statement) {
            $this->execute($statement);
        }

        return implode('', $this->output);
    }

    private function execute(ASTNode $node): mixed
    {
        return match (true) {
            $node instanceof EchoStatement => $this->executeEcho($node),
            $node instanceof PrintStatement => $this->executePrint($node),
            $node instanceof AssignmentStatement => $this->executeAssignment($node),
            $node instanceof IfStatement => $this->executeIf($node),
            $node instanceof WhileStatement => $this->executeWhile($node),
            $node instanceof ForStatement => $this->executeFor($node),
            $node instanceof ForeachStatement => $this->executeForeach($node),
            $node instanceof FunctionDeclaration => $this->executeFunction($node),
            $node instanceof FunctionCall => $this->executeCall($node),
            $node instanceof ReturnStatement => $this->executeReturn($node),
            $node instanceof ClassDeclaration => $this->executeClass($node),
            $node instanceof TryCatchStatement => $this->executeTryCatch($node),
            $node instanceof ThrowStatement => $this->executeThrow($node),
            $node instanceof BreakStatement => throw new BreakException(),
            $node instanceof ContinueStatement => throw new ContinueException(),
            $node instanceof ExitStatement => $this->executeExit($node),
            $node instanceof IncrementExpression => $this->executeIncrement($node),
            $node instanceof DecrementExpression => $this->executeDecrement($node),
            $node instanceof MethodCall => $this->executeMethodCall($node),
            $node instanceof PropertyAccess => $this->executePropertyAccess($node),
            default => $this->evaluate($node),
        };
    }

    private function executeEcho(EchoStatement $node): void
    {
        $value = $this->evaluate($node->expression);
        $this->output[] = $this->stringify($value);
    }

    private function executePrint(PrintStatement $node): void
    {
        $value = $this->evaluate($node->expression);
        $this->output[] = $this->stringify($value) . "\n";
    }

    private function executeAssignment(AssignmentStatement $node): void
    {
        $value = $this->evaluate($node->value);
        $this->currentEnv->set($node->variable, $value);
    }

    private function executeIf(IfStatement $node): void
    {
        if ($this->isTruthy($this->evaluate($node->condition))) {
            foreach ($node->thenBranch as $stmt) {
                $this->execute($stmt);
            }
            return;
        }

        foreach ($node->elseIfBranches as $branch) {
            if ($this->isTruthy($this->evaluate($branch['condition']))) {
                foreach ($branch['body'] as $stmt) {
                    $this->execute($stmt);
                }
                return;
            }
        }

        if ($node->elseBranch !== null) {
            foreach ($node->elseBranch as $stmt) {
                $this->execute($stmt);
            }
        }
    }

    private function executeWhile(WhileStatement $node): void
    {
        while ($this->isTruthy($this->evaluate($node->condition))) {
            try {
                foreach ($node->body as $stmt) {
                    $this->execute($stmt);
                }
            } catch (BreakException) {
                break;
            } catch (ContinueException) {
                continue;
            }
        }
    }

    private function executeFor(ForStatement $node): void
    {
        if ($node->init !== null) {
            $this->execute($node->init);
        }

        while ($node->condition === null || $this->isTruthy($this->evaluate($node->condition))) {
            try {
                foreach ($node->body as $stmt) {
                    $this->execute($stmt);
                }
            } catch (BreakException) {
                break;
            } catch (ContinueException) {
                // Continue to increment
            }

            if ($node->increment !== null) {
                $this->evaluate($node->increment);
            }
        }
    }

    private function executeForeach(ForeachStatement $node): void
    {
        $iterable = $this->evaluate($node->iterable);

        if (!is_array($iterable)) {
            throw new \Exception("Cannot iterate over non-array");
        }

        foreach ($iterable as $key => $value) {
            if ($node->keyVar !== null) {
                $this->currentEnv->set($node->keyVar, $key);
            }
            $this->currentEnv->set($node->valueVar, $value);

            try {
                foreach ($node->body as $stmt) {
                    $this->execute($stmt);
                }
            } catch (BreakException) {
                break;
            } catch (ContinueException) {
                continue;
            }
        }
    }

    private function executeFunction(FunctionDeclaration $node): void
    {
        $this->functions[$node->name] = $node;
    }

    private function executeCall(FunctionCall $node): mixed
    {
        // Check for built-in functions first
        $builtinResult = $this->tryBuiltin($node);
        if ($builtinResult !== null) {
            return $builtinResult['value'];
        }

        if (!isset($this->functions[$node->name])) {
            throw new \Exception("Undefined function: {$node->name}");
        }

        $func = $this->functions[$node->name];
        $args = array_map(fn($arg) => $this->evaluate($arg), $node->arguments);

        $prevEnv = $this->currentEnv;
        $this->currentEnv = new Environment($this->globalEnv);

        // Bind parameters
        foreach ($func->params as $i => $param) {
            $this->currentEnv->define($param, $args[$i] ?? null);
        }

        try {
            foreach ($func->body as $stmt) {
                $this->execute($stmt);
            }
        } catch (ReturnValue $rv) {
            $this->currentEnv = $prevEnv;
            return $rv->value;
        }

        $this->currentEnv = $prevEnv;
        return null;
    }

    private function tryBuiltin(FunctionCall $node): ?array
    {
        $args = array_map(fn($arg) => $this->evaluate($arg), $node->arguments);

        return match ($node->name) {
            'COLLECT' => ['value' => readline($args[0] ?? '') ?: ''],
            'MONETIZE' => ['value' => $this->stringify($args[0] ?? '')],
            'COUNT_USERS' => ['value' => is_array($args[0] ?? null) ? count($args[0]) : 0],
            'BOOST' => $this->builtinBoost($args),
            'ALGORITHM' => $this->builtinSort($args),
            'SHADOWBAN' => ['value' => null],
            'FACT_CHECK_THIS' => ['value' => $this->isTruthy($args[0] ?? null)],
            'TIME_ON_PLATFORM' => ['value' => time()],
            'RANDOM_AD' => ['value' => isset($args[0], $args[1]) ? rand((int)$args[0], (int)$args[1]) : rand()],
            'strlen' => ['value' => strlen((string)($args[0] ?? ''))],
            'substr' => ['value' => substr((string)($args[0] ?? ''), (int)($args[1] ?? 0), $args[2] ?? null)],
            'strtoupper' => ['value' => strtoupper((string)($args[0] ?? ''))],
            'strtolower' => ['value' => strtolower((string)($args[0] ?? ''))],
            'trim' => ['value' => trim((string)($args[0] ?? ''))],
            'explode' => ['value' => explode((string)($args[0] ?? ''), (string)($args[1] ?? ''))],
            'implode' => ['value' => implode((string)($args[0] ?? ''), (array)($args[1] ?? []))],
            'array_push' => $this->builtinArrayPush($node, $args),
            'array_pop' => $this->builtinArrayPop($node),
            'array_keys' => ['value' => array_keys((array)($args[0] ?? []))],
            'array_values' => ['value' => array_values((array)($args[0] ?? []))],
            'in_array' => ['value' => in_array($args[0] ?? null, (array)($args[1] ?? []))],
            'is_array' => ['value' => is_array($args[0] ?? null)],
            'is_string' => ['value' => is_string($args[0] ?? null)],
            'is_int' => ['value' => is_int($args[0] ?? null)],
            'floor' => ['value' => floor((float)($args[0] ?? 0))],
            'ceil' => ['value' => ceil((float)($args[0] ?? 0))],
            'round' => ['value' => round((float)($args[0] ?? 0), (int)($args[1] ?? 0))],
            'abs' => ['value' => abs($args[0] ?? 0)],
            'min' => ['value' => min(...$args)],
            'max' => ['value' => max(...$args)],
            default => null,
        };
    }

    private function builtinBoost(array $args): array
    {
        $value = $args[0] ?? '';
        $this->output[] = "📣 BOOSTED: " . $this->stringify($value) . " 📣\n";
        return ['value' => null];
    }

    private function builtinSort(array $args): array
    {
        $arr = (array)($args[0] ?? []);
        // The ALGORITHM sorts... mysteriously
        shuffle($arr); // Sometimes it promotes engagement
        sort($arr);    // Sometimes it's chronological
        return ['value' => $arr];
    }

    private function builtinArrayPush(FunctionCall $node, array $args): array
    {
        // This is a simplified version
        return ['value' => count($args)];
    }

    private function builtinArrayPop(FunctionCall $node): array
    {
        return ['value' => null];
    }

    private function executeReturn(ReturnStatement $node): void
    {
        $value = $node->value !== null ? $this->evaluate($node->value) : null;
        throw new ReturnValue($value);
    }

    private function executeClass(ClassDeclaration $node): void
    {
        $this->classes[$node->name] = $node;
    }

    private function executeTryCatch(TryCatchStatement $node): void
    {
        try {
            foreach ($node->tryBlock as $stmt) {
                $this->execute($stmt);
            }
        } catch (\Exception $e) {
            if ($e instanceof ReturnValue || $e instanceof BreakException || $e instanceof ContinueException) {
                throw $e;
            }

            $this->currentEnv->set($node->exceptionVar, $e);
            foreach ($node->catchBlock as $stmt) {
                $this->execute($stmt);
            }
        }
    }

    private function executeThrow(ThrowStatement $node): void
    {
        $value = $this->evaluate($node->expression);
        if ($value instanceof \Exception) {
            throw $value;
        }
        throw new \Exception($this->stringify($value));
    }

    private function executeExit(ExitStatement $node): void
    {
        $code = $node->code !== null ? (int)$this->evaluate($node->code) : 0;
        exit($code);
    }

    private function executeIncrement(IncrementExpression $node): mixed
    {
        $current = $this->currentEnv->get($node->variable);
        $new = $current + 1;
        $this->currentEnv->set($node->variable, $new);
        return $node->isPrefix ? $new : $current;
    }

    private function executeDecrement(DecrementExpression $node): mixed
    {
        $current = $this->currentEnv->get($node->variable);
        $new = $current - 1;
        $this->currentEnv->set($node->variable, $new);
        return $node->isPrefix ? $new : $current;
    }

    private function executeMethodCall(MethodCall $node): mixed
    {
        $object = $this->evaluate($node->object);

        if (!$object instanceof ZuckObject) {
            throw new \Exception("Cannot call method on non-object");
        }

        if (!isset($object->methods[$node->method])) {
            throw new \Exception("Undefined method: {$node->method}");
        }

        $method = $object->methods[$node->method];
        $args = array_map(fn($arg) => $this->evaluate($arg), $node->arguments);

        $prevEnv = $this->currentEnv;
        $this->currentEnv = new Environment($this->globalEnv);
        $this->currentEnv->define('$this', $object);

        foreach ($method->params as $i => $param) {
            $this->currentEnv->define($param, $args[$i] ?? null);
        }

        try {
            foreach ($method->body as $stmt) {
                $this->execute($stmt);
            }
        } catch (ReturnValue $rv) {
            $this->currentEnv = $prevEnv;
            return $rv->value;
        }

        $this->currentEnv = $prevEnv;
        return null;
    }

    private function executePropertyAccess(PropertyAccess $node): mixed
    {
        $object = $this->evaluate($node->object);

        if ($object instanceof ZuckObject) {
            return $object->properties[$node->property] ?? null;
        }

        if (is_array($object)) {
            return $object[$node->property] ?? null;
        }

        throw new \Exception("Cannot access property on non-object");
    }

    private function evaluate(ASTNode $node): mixed
    {
        return match (true) {
            $node instanceof LiteralExpression => $node->value,
            $node instanceof VariableExpression => $this->evaluateVariable($node),
            $node instanceof BinaryExpression => $this->evaluateBinary($node),
            $node instanceof UnaryExpression => $this->evaluateUnary($node),
            $node instanceof ArrayExpression => $this->evaluateArray($node),
            $node instanceof ArrayAccess => $this->evaluateArrayAccess($node),
            $node instanceof FunctionCall => $this->executeCall($node),
            $node instanceof NewExpression => $this->evaluateNew($node),
            $node instanceof MethodCall => $this->executeMethodCall($node),
            $node instanceof PropertyAccess => $this->executePropertyAccess($node),
            $node instanceof IncrementExpression => $this->executeIncrement($node),
            $node instanceof DecrementExpression => $this->executeDecrement($node),
            default => throw new \Exception("Unknown expression type: " . get_class($node)),
        };
    }

    private function evaluateVariable(VariableExpression $node): mixed
    {
        return $this->currentEnv->get($node->name);
    }

    private function evaluateBinary(BinaryExpression $node): mixed
    {
        $left = $this->evaluate($node->left);
        $right = $this->evaluate($node->right);

        return match ($node->operator) {
            '+' => $left + $right,
            '-' => $left - $right,
            '*' => $left * $right,
            '/' => $right != 0 ? $left / $right : 0,
            '%' => $right != 0 ? $left % $right : 0,
            '.' => $this->stringify($left) . $this->stringify($right),
            '==' => $left == $right,
            '!=' => $left != $right,
            '<' => $left < $right,
            '>' => $left > $right,
            '<=' => $left <= $right,
            '>=' => $left >= $right,
            '&&' => $this->isTruthy($left) && $this->isTruthy($right),
            '||' => $this->isTruthy($left) || $this->isTruthy($right),
            default => throw new \Exception("Unknown operator: {$node->operator}"),
        };
    }

    private function evaluateUnary(UnaryExpression $node): mixed
    {
        $value = $this->evaluate($node->operand);

        return match ($node->operator) {
            '!' => !$this->isTruthy($value),
            '-' => -$value,
            default => throw new \Exception("Unknown unary operator: {$node->operator}"),
        };
    }

    private function evaluateArray(ArrayExpression $node): array
    {
        $result = [];

        foreach ($node->elements as $element) {
            $value = $this->evaluate($element['value']);

            if ($element['key'] !== null) {
                $key = $this->evaluate($element['key']);
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    private function evaluateArrayAccess(ArrayAccess $node): mixed
    {
        $array = $this->evaluate($node->array);
        $index = $this->evaluate($node->index);

        if (!is_array($array)) {
            throw new \Exception("Cannot access index on non-array");
        }

        return $array[$index] ?? null;
    }

    private function evaluateNew(NewExpression $node): ZuckObject
    {
        if (!isset($this->classes[$node->className])) {
            // Check for built-in Exception
            if ($node->className === 'Exception') {
                $args = array_map(fn($arg) => $this->evaluate($arg), $node->arguments);
                return new \Exception($args[0] ?? 'Error');
            }
            throw new \Exception("Undefined class: {$node->className}");
        }

        $classDecl = $this->classes[$node->className];
        $object = new ZuckObject($node->className);

        // Initialize properties and methods
        foreach ($classDecl->members as $member) {
            if ($member instanceof PropertyDeclaration) {
                $name = ltrim($member->name, '$');
                $object->properties[$name] = $member->defaultValue !== null
                    ? $this->evaluate($member->defaultValue)
                    : null;
            } elseif ($member instanceof MethodDeclaration) {
                $object->methods[$member->name] = $member;
            }
        }

        // Call constructor if exists
        if (isset($object->methods['__construct'])) {
            $args = array_map(fn($arg) => $this->evaluate($arg), $node->arguments);

            $prevEnv = $this->currentEnv;
            $this->currentEnv = new Environment($this->globalEnv);
            $this->currentEnv->define('$this', $object);

            $constructor = $object->methods['__construct'];
            foreach ($constructor->params as $i => $param) {
                $this->currentEnv->define($param, $args[$i] ?? null);
            }

            try {
                foreach ($constructor->body as $stmt) {
                    $this->execute($stmt);
                }
            } catch (ReturnValue) {
                // Constructors don't return values
            }

            $this->currentEnv = $prevEnv;
        }

        return $object;
    }

    private function isTruthy(mixed $value): bool
    {
        if ($value === null) return false;
        if ($value === false) return false;
        if ($value === 0) return false;
        if ($value === 0.0) return false;
        if ($value === '') return false;
        if ($value === []) return false;
        return true;
    }

    private function stringify(mixed $value): string
    {
        if ($value === null) return 'MYSPACE';
        if ($value === true) return 'CONNECTED';
        if ($value === false) return 'DISCONNECTED';
        if (is_array($value)) return 'SOCIAL_GRAPH[' . count($value) . ' users]';
        if ($value instanceof ZuckObject) return 'CORPORATION<' . $value->className . '>';
        if ($value instanceof \Exception) return 'Exception: ' . $value->getMessage();
        return (string)$value;
    }
}
