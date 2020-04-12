
[![Build Status](https://travis-ci.org/intenseprogramming/enhanced-relationlist-type-bundle.svg?branch=master)](https://travis-ci.org/intenseprogramming/enhanced-relationlist-type-bundle)
[![Packagist](https://img.shields.io/packagist/dt/intprog/enhanced-relationlist-type-bundle.svg?style=popout)](https://packagist.org/packages/intprog/enhanced-relationlist-type-bundle)
[![codecov](https://codecov.io/gh/intenseprogramming/enhanced-relationlist-type-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/intenseprogramming/enhanced-relationlist-type-bundle)

# IntProg Enhanced Relation List Type Bundle

This bundle contains the enhanced relation list field type for eZ Platform version 2.x

The enhanced relation list type stores configurable information extending simple relations. It enables an editor to
enhance the data stored with the relation (grouping and attributes).

Additionally the relations may be grouped (via predefined or on the fly created groups).

The behavior of the relation list is completely controlled by the field definition (settings/validation).

Refer to [the general information](doc/GENERAL.md) and [usage documentation](doc/USAGE.md) for more information.

:heavy_exclamation_mark: Upgrade in progress! :heavy_exclamation_mark:

## Installation

The installation uses composer and the general concept for adding bundles to a symfony installation.

Refer to [the installation document](doc/INSTALLATION.md) for more information.

## Configuration

Refer to [the configuration document](doc/CONFIGURATION.md) on how to configure fields of this type.

## Extending

From 1.1.0 on you are able to inject your own attribute types.

Refer to [the extending document](doc/EXTENDING.md) on how to extend the type.
