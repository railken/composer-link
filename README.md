# Composer Link

[![Build Status](https://travis-ci.org/railken/composer-link.svg?branch=master)](https://travis-ci.org/railken/composer-link)

A package has been created to speed-up the development by symlinking packages. Install the package globally.

    composer global require railken/composer-link
    
If this is your first global composer package, you have to add the composer path

    export PATH=$PATH:$HOME/.composer/vendor/bin

## Commands
Package:

    composer-link link
    composer-link unlink

Project:

    composer-link link [vendor/mypackage]
    composer-link unlink [vendor/mypackage]