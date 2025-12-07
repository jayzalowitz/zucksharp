# Zuck# Language Specification

A PHP-inspired esoteric programming language that pays tribute to Mark Zuckerberg.

## File Extension

`.zuck` or `.metaverse`

## Program Structure

Every Zuck# program must start with `<?zuck` and end with `?>`.

## Keywords

| PHP/Hack Equivalent | Zuck# Keyword | Explanation |
|---------------------|---------------|-------------|
| `echo` | `SENATOR_WE_RUN_ADS` | Output to console |
| `print` | `POKE` | Alternative output |
| `$variable =` | `STEAL_DATA` | Variable assignment |
| `if` | `PIVOT_TO_VIDEO` | Conditional start |
| `else` | `PIVOT_TO_METAVERSE` | Else clause |
| `elseif` | `PIVOT_TO_REELS` | Else-if clause |
| `endif` | `END_PIVOT` | End conditional |
| `while` | `MOVE_FAST` | While loop |
| `endwhile` | `BREAK_THINGS` | End while loop |
| `for` | `GROWTH_HACK` | For loop |
| `endfor` | `PLATEAU` | End for loop |
| `foreach` | `HARVEST_USERS` | Foreach loop |
| `endforeach` | `USERS_HARVESTED` | End foreach |
| `function` | `FEATURE` | Function declaration |
| `return` | `IPO` | Return value |
| `class` | `CORPORATION` | Class declaration |
| `new` | `ACQUIRE` | Instantiate object |
| `public` | `OPEN_GRAPH` | Public visibility |
| `private` | `SHADOW_PROFILE` | Private visibility |
| `protected` | `FRIENDS_ONLY` | Protected visibility |
| `true` | `CONNECTED` | Boolean true |
| `false` | `DISCONNECTED` | Boolean false |
| `null` | `MYSPACE` | Null value |
| `try` | `CONGRESSIONAL_HEARING` | Try block |
| `catch` | `TAKE_RESPONSIBILITY` | Catch block |
| `throw` | `BLAME_RUSSIA` | Throw exception |
| `const` | `IMMUTABLE_LIKE_MY_HAIR` | Constant declaration |
| `array` | `SOCIAL_GRAPH` | Array type |
| `string` | `STATUS_UPDATE` | String type |
| `int` | `DAILY_ACTIVE_USERS` | Integer type |
| `float` | `STOCK_PRICE` | Float type |
| `bool` | `FACT_CHECK` | Boolean type |
| `==` | `IS_CONNECTED_TO` | Equality |
| `!=` | `UNFRIENDED` | Not equal |
| `&&` | `AND_ALSO_YOUR_DATA` | Logical AND |
| `\|\|` | `OR_YOUR_FRIENDS_DATA` | Logical OR |
| `!` | `FAKE_NEWS` | Logical NOT |
| `++` | `ENGAGEMENT` | Increment |
| `--` | `CHURN` | Decrement |
| `+` | `MERGE` | Addition |
| `-` | `DIVEST` | Subtraction |
| `*` | `SCALE` | Multiplication |
| `/` | `SPLIT` | Division |
| `%` | `REMAINDER_OF_PRIVACY` | Modulo |
| `[]` | `TIMELINE` | Array access |
| `//` | `REDACTED` | Single-line comment |
| `/* */` | `TERMS_OF_SERVICE ... END_TOS` | Multi-line comment |
| `require` | `ACQUIRE_TALENT` | Include file |
| `include` | `COPY_FROM_SNAPCHAT` | Include file |
| `namespace` | `REBRAND_TO` | Namespace |
| `use` | `INTEGRATE` | Use statement |
| `extends` | `ACQUIRES` | Class inheritance |
| `implements` | `COPIES` | Interface implementation |
| `interface` | `REGULATION` | Interface declaration |
| `abstract` | `METAVERSE_CONCEPT` | Abstract class |
| `static` | `DATACENTER` | Static keyword |
| `this` | `THE_ZUCC` | Self reference |
| `self` | `FACEBOOK_PROPER` | Self reference (static) |
| `parent` | `HARVARD_DROPOUT` | Parent reference |
| `break` | `RAGE_QUIT` | Break statement |
| `continue` | `SCROLL_PAST` | Continue statement |
| `switch` | `A_B_TEST` | Switch statement |
| `case` | `VARIANT` | Case in switch |
| `default` | `CONTROL_GROUP` | Default case |
| `die/exit` | `SHUTDOWN_LIKE_VINE` | Exit program |
| `global` | `WORLDWIDE_EXCEPT_CHINA` | Global scope |

## String Delimiters

- Use `"..."` for regular strings (called "status updates")
- Use `'...'` for literal strings (called "terms no one reads")
- String concatenation uses `.` (the "timeline merge" operator)

## Special Values

- `ZUCKS_AGE` - Returns 2000 (birth year reference)
- `SWEET_BABY_RAYS` - Magic constant, always delicious (returns "BBQ")
- `LIZARD_PERSON` - Self-referential joke constant (returns THE_ZUCC)

## Example Syntax

```zuck
<?zuck

REDACTED This is a comment

STEAL_DATA $greeting = "Hello, World!";
SENATOR_WE_RUN_ADS $greeting;

STEAL_DATA $users = 0;

MOVE_FAST ($users < 3000000000) {
    $users ENGAGEMENT;
    POKE $users;
} BREAK_THINGS

FEATURE greet($name) {
    IPO "Hello, " . $name . "! Your data is safe with us.";
}

SENATOR_WE_RUN_ADS greet("Senator");

?>
```

## Data Structures

### Arrays (SOCIAL_GRAPH)

```zuck
STEAL_DATA $friends = SOCIAL_GRAPH["Tom", "Eduardo", "Sean"];
STEAL_DATA $user = SOCIAL_GRAPH[
    "name" => "Mark",
    "net_worth" => "varies_wildly",
    "human" => DISCONNECTED
];
```

### Classes (CORPORATION)

```zuck
CORPORATION SocialNetwork {
    SHADOW_PROFILE $userData;
    OPEN_GRAPH $monthlyActiveUsers;

    OPEN_GRAPH FEATURE __construct() {
        THE_ZUCC->$userData = SOCIAL_GRAPH[];
        THE_ZUCC->$monthlyActiveUsers = 0;
    }

    OPEN_GRAPH FEATURE harvestData($user) {
        THE_ZUCC->$userData TIMELINE[] = $user;
        IPO CONNECTED;
    }
}

STEAL_DATA $meta = ACQUIRE SocialNetwork();
$meta->harvestData("your_preferences");
```

## Control Flow

### Conditionals

```zuck
PIVOT_TO_VIDEO ($stockPrice > 300) {
    SENATOR_WE_RUN_ADS "Shareholders happy!";
} PIVOT_TO_REELS ($stockPrice > 200) {
    SENATOR_WE_RUN_ADS "Could be worse.";
} PIVOT_TO_METAVERSE {
    SENATOR_WE_RUN_ADS "Time to pivot!";
} END_PIVOT
```

### Error Handling

```zuck
CONGRESSIONAL_HEARING {
    BLAME_RUSSIA ACQUIRE Exception("Data breach!");
} TAKE_RESPONSIBILITY (Exception $e) {
    SENATOR_WE_RUN_ADS "We take responsibility. " . $e->getMessage();
}
```

## Built-in Functions

| Function | Description |
|----------|-------------|
| `COLLECT()` | Read user input |
| `MONETIZE($data)` | Convert to string |
| `COUNT_USERS($array)` | Count array elements |
| `BOOST($post)` | Output with extra formatting |
| `ALGORITHM($array)` | Sort array (mysteriously) |
| `SHADOWBAN($var)` | Unset variable |
| `FACT_CHECK_THIS($val)` | Check if value is truthy |
| `TIME_ON_PLATFORM()` | Current timestamp |
| `RANDOM_AD()` | Random number |

## Operators Precedence

Same as PHP, but remember: engagement metrics always take priority.

## Runtime Behavior

- All errors are logged but shown to users as "Something went wrong"
- Memory is unlimited (we have data centers)
- Execution time has no limit (infinite scroll)
- All variables are tracked for debugging purposes
