Lozeil
======

Lozeil is a PHP-based cashflow management web application. Its goal is to improve and simplify the way that companies manage their cash.
This involves bank's statement importation from standard format, real-time monitoring, statistics, simulations...


## Installation

### Requirement
* Server supporting PHP
* Mysql database

### Build your own Lozeil

First, clone a copy of Lozeil's git repo by running:
```bash
git clone git://github.com/noparking/lozeil.git
```

Setup your installation by running bot.php located in cli/, give the database's information and create a default user:
```bash
php bot.php --setup
```

Your done! Try running index.php to test your installation.

## Usage

### Import your bank statement
> to-do

### Categorize
> to-do

### Statistics
> to-do

### Simulations
> to-do

### See the machine learning effect
> to-do

## Running the unit tests

To run the unit tests you need to update the submodule simpletest:
```bash
git submodule init
```
```bash
git submodule update
```

Once done, you're all set up! The tests are lacated in tests/unit/.

## Links

* Repository: git://github.com/noparking/lozeil.git
* Issues:  > to-do link to bug tracker..
* Simpletest: <https://github.com/simpletest/simpletest>
