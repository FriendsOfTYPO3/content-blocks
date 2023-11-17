.. include:: /Includes.rst.txt
.. _field_type_relation:

========
Relation
========

The `Relation` type can handle relations to other record types. They will be
available to select from the Record Selector.

It corresponds with the TCA :php:`type => 'group'`.

SQL overrides via `alternativeSql` allowed: yes.

Settings
========

.. confval:: allowed

   :Required: true
   :Type: string (table name, comma-separated)

   One or more tables, that should be referenced.

.. confval:: maxitems

   :Required: false
   :Type: integer

   Maximum number of items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems

   :Required: false
   :Type: integer

   Minimum number of items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-group>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/relation
    fields:
      - identifier: record_select
        type: Relation
        allowed: 'some_table'

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/relation
    fields:
      - identifier: page_select
        type: Relation
        allowed: 'pages'
        maxitems: 1,
        suggestOptions:
          default:
            additionalSearchFields: 'nav_title, url'
            addWhere: 'AND pages.doktype = 1'
