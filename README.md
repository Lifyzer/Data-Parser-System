# 🍏 Lifyzer Data Parser 🍓

_Simple script that parses data from open source food databases to the standard Lifyzer food DB structure._


## The Requirements ⚙

* 🐘 [PHP 7.1](http://php.net/releases/7_1_0.php) or higher
* 🎷 [Composer](https://getcomposer.org)


### Can be slow... with big databases

When running `transform-csv-to-valid-file.php` with >= 2.4 GB CSV file for the food database provider, it can take nearly 5 days. Just keep your Web browser running and wait :) 


## Who Did This...? 😉

Made with ❤️ by [Pierre-Henry Soria](https://pierrehenry.be)! (and in good health **thanks [Lifyzer App](https://lifyzer.com)**! 😸)


## Contact 📧

Email me at *pierre {[AT]} soria {[D0T]} email*


## Tests 👷

To run the test suites:

```bash
$ composer install
$ php vendor/bin/phpunit

```
