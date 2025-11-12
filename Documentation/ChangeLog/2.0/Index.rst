.. include:: /Includes.rst.txt
.. _changelog-2.0:

===
2.0
===

Content Blocks version 2.0 adds support for TYPO3 v14 and removes support for TYPO3 v13.

..  contents::

Feature
=======

Field Type Country
------------------

A new Field Type :ref:`Country <field_type_country>` is added. This type provides a list of countries for selection.

.. code-block:: yaml

    name: example/country
    fields:
      - identifier: country
        type: Country
        required: true
        default: 'DE'
        labelField: 'localizedName'
        prioritizedCountries:
          - DE
          - AT
          - CH

Breaking
========

Changes to FieldType attribute parameters
-----------------------------------------

Searchable fields are now automatically considered in backend search.
In order to provide the possibility to remove a field from search,
the new trait :php:`TYPO3\CMS\ContentBlocks\FieldType\WithSearchableProperty`
can be included. See Content Blocks Core Field Types for an example how
to use it.

Before

.. code-block:: php

    #[FieldType(name: 'Money', tcaType: 'number', searchable: true)]
    final class MoneyFieldType extends AbstractFieldType
    {}

After

.. code-block:: php

    #[FieldType(name: 'Money', tcaType: 'number')]
    final class MoneyFieldType extends AbstractFieldType
    {}
