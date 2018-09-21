# php-graphql-client-generator-cli
## What is this?
This is CLI for [PHP GraphQL client generator](https://github.com/kolah/php-graphql-client-generator), which generates a strongly typed PHP GraphQL client out of the GraphQL schema file.

At the time of creating this tool there was no decent way to utilize GraphQL APIs from PHP.

This tool is based on [camuthig/php-graphql-client-generator](https://github.com/camuthig/php-graphql-client-generator)

## Requirements
* PHP 7.1

## Installation

#### PHAR (recommended)
To run installer and download PHAR with its signature file to current directory:

`curl -LSs https://kolah.github.io/php-graphql-client-generator-cli/installer.php | php`

> NOTE: If you want to rename command in order to get rid of `.phar` extension or move the tool to other directory, please note that PHAR binary is signed and requires `.pubkey` file to work, so you will need to rename signature file accordingly, fe. `mv gql2php.phar gql2php && mv gql2php.phar.pubkey gql2php.pubkey` 

#### Composer (globally)

`composer global require kolah/php-graphql-client-generator-cli`


## Usage

### Client generation
In order to generate client, use `gql2php generate`. 
The `generate` command expects some required parameters:
* `-s` or `--schema`: path to GraphQL schema file, fe.: `./schema.graphqls`
* `-ns` or `--namespace`: a namespace to put generated code, fe. `"Kolah\Client"`
* `-o` or `--output-dir`: base directory for outputting generated code, fe. `src/Kolah/Client`

`gql2php` provides ability to override generated code by providing a map for GraphQL types to Fully Qualified Class Name:
* `-m` or `--map` "Time:Kolah\Client\Extended\Time"

In order to use generated code, the base client is required as a dependency: 

`composer require kolah/php-graphql-client-base`

### Self-update (PHAR version)
This application provides self-update mechanism. To check for updates and automatically update the tool, use `gql2php update`. In case of finding a newer version, the old one is backed up.

You can rollback to the previous version, if there is one stored using `gql2php update --rollback`.

> NOTE: For composer global install, use `composer global update kolah/php-graphql-client-generator-cli`