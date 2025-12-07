# Contributing to Zuck#

Thanks for your interest in contributing to Zuck#! We'd love to harvest your contributions.

## Development Setup

### Prerequisites

- PHP 8.1 or higher
- Composer

### Getting Started

1. **Clone the repository:**
   ```bash
   git clone https://github.com/jayzalowitz/zucksharp.git
   cd zucksharp
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Verify the installation:**
   ```bash
   ./bin/zuck examples/hello.zuck
   ```

   You should see:
   ```
   Hello, World!
   Your data is safe with us. Trust us.
   ```

## Running Tests

We use PHPUnit for testing. Run the test suite with:

```bash
composer test
```

### Test Structure

- `tests/LexerTest.php` - Tests for tokenization
- `tests/ParserTest.php` - Tests for AST generation
- `tests/InterpreterTest.php` - Tests for execution
- `tests/ExamplesTest.php` - Integration tests for example files

When adding new features, please add corresponding tests.

## Code Style

We use PHP CS Fixer for consistent code formatting.

**Check your code style:**
```bash
composer lint
```

**Auto-fix code style issues:**
```bash
composer lint:fix
```

## Static Analysis

We use PHPStan for static analysis:

```bash
composer analyze
```

## Making Changes

### Branch Naming

Use descriptive branch names:
- `feature/add-switch-statement`
- `fix/lexer-string-escape`
- `docs/update-readme`

### Commit Messages

Write clear, concise commit messages:
- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Keep the first line under 72 characters

### Pull Request Process

1. Fork the repository and create your branch from `main`
2. Make your changes and add tests
3. Ensure all tests pass: `composer test`
4. Check code style: `composer lint`
5. Run static analysis: `composer analyze`
6. Update documentation if needed
7. Submit a pull request

## Adding New Keywords

Zuck# keywords should be satirical references to Facebook/Meta culture. When adding new keywords:

1. Add the keyword mapping in `src/Lexer.php` in the `$keywords` array
2. Add parser support in `src/Parser.php`
3. Add interpreter support in `src/Interpreter.php`
4. Add tests for the new feature
5. Update `LANGUAGE.md` with the new keyword
6. Add an example in the `examples/` directory if applicable

### Keyword Naming Guidelines

Keywords should be:
- All uppercase with underscores
- Satirical references to Facebook/Meta/Zuckerberg
- Memorable and somewhat descriptive of their function

Examples:
- `SENATOR_WE_RUN_ADS` for `echo` (famous testimony quote)
- `STEAL_DATA` for variable assignment (what else?)
- `PIVOT_TO_VIDEO` for `if` (remember that strategy?)

## Project Structure

```
zucksharp/
├── bin/
│   └── zuck           # CLI entry point
├── src/
│   ├── Lexer.php      # Tokenizer
│   ├── Parser.php     # AST builder
│   ├── Interpreter.php # Execution engine
│   └── ZuckSharp.php  # Main orchestrator
├── tests/
│   ├── LexerTest.php
│   ├── ParserTest.php
│   ├── InterpreterTest.php
│   └── ExamplesTest.php
├── examples/          # Example Zuck# programs
├── docs/              # GitHub Pages documentation
├── LANGUAGE.md        # Language specification
└── README.md          # Project overview
```

## Questions?

If you have questions, feel free to open an issue with the `question` label.

---

*"Done is better than perfect."* - Move fast and contribute!
