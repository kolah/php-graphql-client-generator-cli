# php-graphql-client-generator-cli
## What is this?
This is CLI for PHP GraphQL client generator, which generates a strongly typed PHP GraphQL client out of the GraphQL schema file.

At the time of creating this tool there was no decent way to utilize GraphQL APIs from PHP.

## Installation

#### Composer

`composer global require kolah/php-graphql-client-generator-cli`

 

## Usage
In order to generate client, use `gql2php generate`. 
The `generate` command expects some required parameters:
* `-s` or `--schema`: path to GraphQL schema file, fe.: `./schema.graphqls`
* `-ns` or `--namespace`: a namespace to put generated code, fe. `"Kolah\Client"`
* `-o` or `--output-dir`: base directory for outputting generated code, fe. `src/Kolah/Client`

`gql2php` provides ability to override generated code by providing a map for GraphQL types to Fully Qualified Class Name:
* `-m` or `--map` "Time:Kolah\Client\Extended\Time"

In order to use generated code, the base client is required as a dependency: 

`composer require kolah/php-graphql-client-base`

