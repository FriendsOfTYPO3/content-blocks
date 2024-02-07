.. include:: /Includes.rst.txt
.. _field_type_language:

========
Language
========

:php:`type => 'language' // TCA`

The :yaml:`Language` type is for rendering a select box with all available languages for the current installation.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/language
    fields:
      - identifier: language
        type: Language
