.. include:: /Includes.rst.txt
.. _field_type_reference:

=========
Reference
=========

The `Reference` type can handle relations to other record types. They will be
available to select from the Record Selector.

It corresponds with the TCA :php:`type => 'group'`.

SQL overrides via `alternativeSql` allowed: yes.

Properties
==========

.. rst-class:: dl-parameters

relationship
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` string (table name, comma-separated)
   :sep:`|`

   One or more tables, that should be referenced.

maxitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Maximum number of items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

minitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Minimum number of items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-group>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    group: common
    fields:
      - identifier: pageSelect
        type: Reference
        properties:
          allowed: 'pages'

Advanced / use case
-------------------

.. code-block:: yaml

    group: common
    fields:
      - identifier: pageSelect
        type: Reference
        properties:
          allowed: 'pages'
          maxitems: 1,
          minitems: 0,
          suggestOptions:
            'default' => [
              'additionalSearchFields' => 'nav_title, url',
              'addWhere' => 'AND pages.doktype = 1',
            ],
