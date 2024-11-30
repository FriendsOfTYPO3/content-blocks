.. include:: /Includes.rst.txt
.. _field_type_relation:

========
Relation
========

:php:`type => 'group' // TCA`

The :yaml:`Relation` type can handle relations to other record types. They will
be available to select from the Record Selector.

Settings
========

.. confval:: allowed
   :name: relation-allowed
   :required: true
   :type: string (table name, comma-separated)

   One or more tables, that should be referenced.

   This table can be defined by another Content Block, but can also be an
   existing table defined by the Core or another extension.

.. confval:: maxitems
   :name: relation-maxitems
   :required: false
   :type: integer

   Maximum number of items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems
   :name: relation-minitems
   :required: false
   :type: integer

   Minimum number of items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

.. confval:: relationship
   :name: relation-relationship
   :required: false
   :type: string
   :default: oneToMany

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), :yaml:`manyToOne` and
   :yaml:`oneToOne`. In case of a [x]toOne relation, the processed field will
   be filled directly with the record instead of a collection of records. In
   addition, :yaml:`maxitems` will be automatically set to :yaml:`1`.

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
        maxitems: 1
        suggestOptions:
          default:
            additionalSearchFields: 'nav_title, url'
            addWhere: 'AND pages.doktype = 1'
