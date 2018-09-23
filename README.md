# PHP Turing Machine

This is a PHP 7 CLI implementation of a Busy beaver class Turing machine.

It can run n-state 2-symbol Busy beaver instructions read in from a json formatted array.

<b>Notice</b>:
The source code of this Turing machine is written in a non traditional way of writing PHP 7, including but not limited to the use of `goto` instead of functions and loops.
This is done purely for speed optimizations.

All the Busy beaver examples in this project are taken from Wikipedia:
[Busy beaver examples](https://en.wikipedia.org/wiki/Busy_beaver#Examples) (2018-09-20).

## Getting Started

Run the Turing machine in a CLI:

```php turingmachine.php```

```./turingmachine.php```

This will trigger the demo instructions, which is a Busy beaver 4-state 2-symbol Turing machine.

<b>Flags available:</b>

```--json```
allows you to pipe in a json array containing the Busy beaver Turing machine instructions

```--print-tape```
makes the Turing machine run slower, but prints out the tape from each step

## Instructions setup

The instructions are written in the following form:

`"0A": "1RB"`

Where `0` is what the head is reading, `A` is the current state, `1` is the symbol to write,
`R` is the movement <i>(L = Left, R = Right, N = No movement)</i>, and `B` is the next state.

<b>Example instructions (2-state 2-symbol Busy beaver):</b>

````json
{
    "0A": "1RB",
    "1A": "1LB",
    "0B": "1LA",
    "1B": "1RH"
}
````

## Examples

Pipe from file:

```cat examples/busybeaver-4-state-2-symbol.json | ./turingmachine.php --json```

Pipe from echo:

```echo '{"0A":"1RB","1A":"1LB","0B":"1LA","1B":"1RH"}' | ./turingmachine.php --json```

## Authors

* **Joakim Winum Lien** - *Initial work* - [joakimwinum](https://github.com/joakimwinum)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
