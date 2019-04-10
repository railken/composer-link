# Skeleton Amethyst Tool

[![Build Status](https://travis-ci.org/railken/cli-amethyst.svg?branch=master)](https://travis-ci.org/railken/cli-amethyst)

A package has been created to speed-up the development. Install the package globally.

    composer global require railken/cli-amethyst
    
If this is your first global composer package, you have to add the composer path

    export PATH=$PATH:$HOME/.composer/vendor/bin

## Commands

| Name     | Description                                   |
|----------|-----------------------------------------------|
| amethyst lib:init | Initialize the library as an amethyst-package |
| amethyst lib:data | Generate all files to add a brand new entity  |
| amethyst lib:doc  | Generate the documentation of the library     |
| amethyst test     | Test your package using the standard rules    |