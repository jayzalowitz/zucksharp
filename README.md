# Zuck# (ZuckSharp)

> Move Fast and Break Things. Then apologize to Congress about it.

Zuck# is a PHP-inspired esoteric programming language that pays tribute to (and lightly roasts) Mark Zuckerberg. It's fully functional and can run real programs!

**[Live Playground & Documentation](https://jayzalowitz.github.io/zucksharp/)**

## Features

- **Full PHP-like syntax** - Variables, functions, classes, loops, conditionals
- **Interactive REPL** - Test code in real-time
- **Hilarious keywords** - `STEAL_DATA`, `SENATOR_WE_RUN_ADS`, `PIVOT_TO_METAVERSE`
- **Complete OOP support** - Classes, inheritance, visibility modifiers
- **Error handling** - Try/catch with `CONGRESSIONAL_HEARING` blocks

## Requirements

- **PHP 8.1 or higher**
- Composer (optional, for development)

## Installation

### Quick Start (No Dependencies)

```bash
git clone https://github.com/jayzalowitz/zucksharp.git
cd zucksharp
chmod +x bin/zuck
./bin/zuck --help
```

### Via Composer (Development)

```bash
git clone https://github.com/jayzalowitz/zucksharp.git
cd zucksharp
composer install
./bin/zuck --help
```

### Global Installation

```bash
git clone https://github.com/jayzalowitz/zucksharp.git
cd zucksharp
chmod +x bin/zuck
sudo ln -s $(pwd)/bin/zuck /usr/local/bin/zuck
```

Now you can run `zuck` from anywhere.

## Usage

```bash
# Run a Zuck# program
./bin/zuck examples/hello.zuck

# Enter REPL mode (interactive)
./bin/zuck

# Run with debug output
./bin/zuck --debug examples/fizzbuzz.zuck

# Show help
./bin/zuck --help
```

## Hello World

```zuck
<?zuck

SENATOR_WE_RUN_ADS "Hello, World!\n";
SENATOR_WE_RUN_ADS "Your data is safe with us. Trust us.\n";

?>
```

## Language Features

### Variables (STEAL_DATA)

```zuck
STEAL_DATA $name = "Mark";
STEAL_DATA $isHuman = CONNECTED;      // true
STEAL_DATA $empathy = MYSPACE;         // null
```

### Output (SENATOR_WE_RUN_ADS / POKE)

```zuck
SENATOR_WE_RUN_ADS "Hello, Senator!\n";
POKE "This also prints with newline";
```

### Conditionals (PIVOT_TO_VIDEO)

```zuck
PIVOT_TO_VIDEO ($stockPrice > 300) {
    SENATOR_WE_RUN_ADS "Shareholders happy!\n";
} PIVOT_TO_REELS ($stockPrice > 200) {
    SENATOR_WE_RUN_ADS "Could be worse.\n";
} PIVOT_TO_METAVERSE {
    SENATOR_WE_RUN_ADS "Time to pivot!\n";
} END_PIVOT
```

### Loops (MOVE_FAST / GROWTH_HACK)

```zuck
REDACTED While loop
MOVE_FAST ($users < 3000000000) {
    $users ENGAGEMENT;
} BREAK_THINGS

REDACTED For loop
GROWTH_HACK ($i = 1; $i <= 10; $i ENGAGEMENT) {
    SENATOR_WE_RUN_ADS $i . "\n";
} PLATEAU

REDACTED Foreach
HARVEST_USERS ($pivots AS $pivot) {
    SENATOR_WE_RUN_ADS $pivot . "\n";
} USERS_HARVESTED
```

### Functions (FEATURE / IPO)

```zuck
FEATURE greet($name) {
    IPO "Hello, " . $name . "!";
}

STEAL_DATA $greeting = greet("Senator");
```

### Classes (CORPORATION)

```zuck
CORPORATION SocialNetwork {
    SHADOW_PROFILE $userData;
    OPEN_GRAPH $name;

    OPEN_GRAPH FEATURE __construct($name) {
        THE_ZUCC->$name = $name;
    }

    OPEN_GRAPH FEATURE harvestData($data) {
        IPO CONNECTED;
    }
}

STEAL_DATA $fb = ACQUIRE SocialNetwork("Facebook");
$fb->harvestData("everything");
```

### Error Handling (CONGRESSIONAL_HEARING)

```zuck
CONGRESSIONAL_HEARING {
    BLAME_RUSSIA ACQUIRE Exception("Data breach!");
} TAKE_RESPONSIBILITY (Exception $e) {
    SENATOR_WE_RUN_ADS "We take this seriously.\n";
}
```

### Arrays (SOCIAL_GRAPH)

```zuck
STEAL_DATA $friends = SOCIAL_GRAPH["Tom", "Eduardo", "Sean"];
STEAL_DATA $user = SOCIAL_GRAPH[
    "name" => "Mark",
    "human" => DISCONNECTED
];
```

## Keyword Reference

| PHP/Hack | Zuck# | Why |
|----------|-------|-----|
| `echo` | `SENATOR_WE_RUN_ADS` | The famous congressional testimony |
| `$x =` | `STEAL_DATA` | What else would we call it? |
| `if` | `PIVOT_TO_VIDEO` | Remember when that was the strategy? |
| `elseif` | `PIVOT_TO_REELS` | Then this happened |
| `else` | `PIVOT_TO_METAVERSE` | The current pivot |
| `while` | `MOVE_FAST` | The motto |
| `endwhile` | `BREAK_THINGS` | The other half |
| `for` | `GROWTH_HACK` | Gotta get those DAUs |
| `function` | `FEATURE` | Ship features |
| `return` | `IPO` | The ultimate return |
| `class` | `CORPORATION` | It's basically the same thing |
| `new` | `ACQUIRE` | How Meta gets things |
| `public` | `OPEN_GRAPH` | Remember that API? |
| `private` | `SHADOW_PROFILE` | The secret data |
| `this` | `THE_ZUCC` | Self-reference |
| `true` | `CONNECTED` | Facebook's whole thing |
| `false` | `DISCONNECTED` | The opposite |
| `null` | `MYSPACE` | Dead and empty |
| `try` | `CONGRESSIONAL_HEARING` | Where exceptions happen |
| `catch` | `TAKE_RESPONSIBILITY` | (sort of) |
| `throw` | `BLAME_RUSSIA` | The classic move |
| `++` | `ENGAGEMENT` | More is always better |
| `--` | `CHURN` | Users leaving |
| `==` | `IS_CONNECTED_TO` | Are they friends? |
| `!=` | `UNFRIENDED` | Not anymore |
| `&&` | `AND_ALSO_YOUR_DATA` | We want it all |
| `\|\|` | `OR_YOUR_FRIENDS_DATA` | Them too |
| `//` | `REDACTED` | Comments are redacted |

## Magic Constants

- `ZUCKS_AGE` - Returns 2000 (birth year)
- `SWEET_BABY_RAYS` - Returns "BBQ" (from the smoking meats video)
- `LIZARD_PERSON` - Self-referential constant

## Built-in Functions

| Function | Description |
|----------|-------------|
| `COLLECT()` | Read user input |
| `MONETIZE($val)` | Convert to string |
| `COUNT_USERS($arr)` | Count array elements |
| `BOOST($msg)` | Print with emphasis |
| `ALGORITHM($arr)` | Sort (mysteriously) |
| `SHADOWBAN($var)` | Unset variable |
| `FACT_CHECK_THIS($val)` | Check truthiness |
| `TIME_ON_PLATFORM()` | Current timestamp |
| `RANDOM_AD()` | Random number |

## Architecture

The interpreter consists of three main components:

```
src/
├── Lexer.php       # Tokenizes Zuck# source code
├── Parser.php      # Builds AST from tokens
├── Interpreter.php # Executes the AST
└── ZuckSharp.php   # Main entry point
```

## Examples

See the `examples/` directory:

| File | Description |
|------|-------------|
| `hello.zuck` | Hello World |
| `variables.zuck` | Variable usage |
| `loops.zuck` | Loop examples |
| `conditionals.zuck` | If/else examples |
| `functions.zuck` | Function definitions |
| `classes.zuck` | Class/object examples |
| `error_handling.zuck` | Try/catch |
| `fizzbuzz.zuck` | Classic FizzBuzz |

## Development

### Running Tests

```bash
composer test
# or
./vendor/bin/phpunit
```

### Code Quality

```bash
# Lint check
composer lint

# Auto-fix lint issues
composer lint:fix

# Static analysis
composer analyze
```

### CI/CD

This project uses GitHub Actions for:
- Running tests on PHP 8.1, 8.2, and 8.3
- Code style checking with PHP CS Fixer
- Static analysis with PHPStan

## Contributing

We'd love to harvest your contributions! See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

Remember: Done is better than perfect.

## License

MIT License - Move fast and don't worry about licensing.

---

*"I was human. I am human. I will continue to be human."* - The Zucc, probably

*blinks manually*
