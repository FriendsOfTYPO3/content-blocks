.. include:: /Includes.rst.txt
.. _changelog-1.6:

===
1.6
===

Content Blocks version 1.6 adds a collection of small new features.

..  contents::

Feature
=======

Identifier `alias` for fields
-----------------------------

You can now define an :yaml:`alias` for fields, which will then be used instead
of the :yaml:`identifier` in your Fluid templates. This has two main advantages:

1. You are not forced to use snake_case in Fluid, just because it is better
   suited for database column names.
2. You can use semantic names when re-using shared, generic fields in the
   context of your Content Block.

.. code-block:: yaml

    name: example/cta
    fields:
      - identifier: header
        alias: title

New option `hideInUid` for Record Types
---------------------------------------

It is now possible to explicitly hide Record Types in the record overview
by defining :yaml:`hideInUid: true`. This is already done automatically when
the Record Type is used as a child item in Collections.
