.. include:: /Includes.rst.txt
.. _field_type_slug:

====
Slug
====

The `Slug` type generates a slug field, which generates a unique string for the
record.

It corresponds with the TCA :php:`type => 'slug'`.

SQL overrides via `alternativeSql` allowed: no.

Settings
========

.. confval:: eval

   :Required: false
   :Type: string

   :yaml:`unique`, :yaml:`uniqueInSite` or :yaml:`uniqueInPid`.

.. confval:: generatorOptions

   :Required: false
   :Type: array

   Options related to the generation of the slug. Keys:

   fields (array)
      An array of fields to use for the slug generation. Adding multiple fields
      to the simple array results in a concatenation. In order to have fallback
      fields, a nested array must be used.

   Example:

   .. code-block:: yaml

      generatorOptions:
        fields:
          - header

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-slug>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/slug
    group: common
    fields:
      - identifier: slug
        type: Slug
        eval: unique
        generatorOptions:
          fields:
            - header

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/slug
    group: common
    fields:
      - identifier: slug
        type: Slug
        eval: unique
        generatorOptions:
          fields:
            -
              - header
              - fallbackField
            - date
