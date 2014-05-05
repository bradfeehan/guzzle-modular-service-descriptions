guzzle-modular-service-descriptions
===================================

[![Build Status][build-badge]][build]
[![Code Coverage][coverage-badge]][coverage]
[![Scrutinizer Code Quality][quality-badge]][quality]

[build]: <https://travis-ci.org/bradfeehan/guzzle-modular-service-descriptions>
[build-badge]: <https://travis-ci.org/bradfeehan/guzzle-modular-service-descriptions.svg>
[coverage]: <https://scrutinizer-ci.com/g/bradfeehan/guzzle-modular-service-descriptions/>
[coverage-badge]: <https://scrutinizer-ci.com/g/bradfeehan/guzzle-modular-service-descriptions/badges/coverage.png?s=f1038391bf34bf9b092b9dd6a692603e28ad5c5e>
[quality]: <https://scrutinizer-ci.com/g/bradfeehan/guzzle-modular-service-descriptions/>
[quality-badge]: <https://scrutinizer-ci.com/g/bradfeehan/guzzle-modular-service-descriptions/badges/quality-score.png?s=6e2453ad0fce960356d875ef2307677fe77674c2>



A better ServiceDescriptionLoader for [Guzzle 3.x]

[Guzzle 3.x]: <https://github.com/guzzle/guzzle3>




Features
--------

Guzzle's [service descriptions] make it easy to describe APIs. A
service description takes the form of a JSON object (or PHP associative
array) that describes all the available operations and data models
supported by the API. However, for large or poorly-designed APIs, the
service description can quickly become hard to manage.

This project offers a replacement ServiceDescriptionLoader
implementation, which allows for much more flexibility when writing
service descriptions. It supports:

* Separating the service description arbitrarily into many files
  ("modular" service descriptions)
* Alternative formats (plain text and YAML)

[service descriptions]: <https://github.com/guzzle/guzzle3/blob/master/docs/webservice-client/guzzle-service-descriptions.rst>




Installation
------------

To get this library in to an existing project, the best way is to use
[Composer](http://getcomposer.org).

1. Add `bradfeehan/guzzle-modular-service-descriptions` as a Composer
   dependency in your project's [`composer.json`][composer-json] file:

    ```json
    {
        "require": {
            "bradfeehan/guzzle-modular-service-descriptions": "~1.0"
        }
    }
    ```

2. If you haven't already, download and
   [install Composer][composer-download]:

    ```bash
    $ curl -sS https://getcomposer.org/installer | php
    ```

3. [Install your Composer dependencies][composer-install]:

    ```bash
    $ php composer.phar install
    ```

4. Set up [Composer's autoloader][composer-loader]:

    ```php
    require_once 'vendor/autoload.php';
    ```

[composer-json]: <http://getcomposer.org/doc/01-basic-usage.md#the-require-key>
    "More on the composer.json format"
[composer-download]: <http://getcomposer.org/doc/01-basic-usage.md#installation>
    "More detailed installation instructions on the Composer site"
[composer-install]: <http://getcomposer.org/doc/01-basic-usage.md#installing-dependencies>
    "More detailed instructions on the Composer site"
[composer-loader]: <http://getcomposer.org/doc/01-basic-usage.md#autoloading>
    "More information about the autoloader on the Composer site"




Usage
-----

In contrast to typical Guzzle service descriptions, a modular service
description is implemented as a directory. The format of the directory
is very flexible. The directory structure is reflected in the heirarchy
of the service description data.



#### Format

A file in the root of a modular service description directory defines
the key with the name of the file. The content of the file defines the
value of that key. As an example, consider the following directory
structure:

```
my_service_description/
├── name.txt
└── operations.json
```

The `my_service_description` directory can be loaded as a modular
service description. The content inside `name.txt` will be put into the
key `name` at the top level of the service description. The content of
`operations.json` will be the value for the `operations` key in the
service description. (So, to be a valid service description,
`operations.json` should contain a JSON object, defining all the
operations).


##### Nested directories

Another, more complex example:

```
complicated_service_description/
├── name.txt
└── operations/
    ├── ComplexOperation/
    │   └── parameters.yml
    └── ComplexOperation.json
```

Again, `name.txt` will contain the value for the `name` key. However,
this time, the `operations` key is represented by a directory. The
files inside are converted to nested keys in the resulting service
description. So this example will result in the following
representation:

```json
{
    "name": "[content of name.txt]",
    "operations": {
        "ComplexOperation": {
            // The keys defined in ComplexOperation.json
            // will be inserted here
            // ...
            "parameters": "[parsed content of parameters.yml]"
        }
    }
}
```


##### `__index` files

Any files named `__index.[ext]` define the contents of the directory the
file is in, rather than the name of the file. It's essentially an
"empty" name. This concept is similar to Python's `__init__.py`, which
makes the *directory* the package, rather than the *file*.



### Loading the service description

To load a modular service description, load it using the included
loader, and add it to the web service client instance:

```php
use BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader;
use Guzzle\Service\Client;

// Create a client somehow
$client = Client::factory();

// Instantiate the modular service description loader
$loader = new ServiceDescriptionLoader();

// Point the loader at the modular service description directory
$description = $loader->load('/path/to/service_description');

// Add the service description to the client
$client->setDescription($description);

// Done!
$command = $client->getCommand('MyCommand');

// ...
```

