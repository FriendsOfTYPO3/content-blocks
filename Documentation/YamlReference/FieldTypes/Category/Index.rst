.. include:: /Includes.rst.txt
.. _field_type_category:

========
Category
========

The :yaml:`Category` type can handle relations to categories. The categories are
taken from the system table :sql:`sys_categories`.

Settings
========

..  confval-menu::
    :name: confval-category-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: relationship
   :name: category-relationship
   :required: false
   :type: string

   Depending on the relationship, the category relations is stored (internally)
   in a different way. Possible keywords are `oneToOne`, `oneToMany` or
   `manyToMany` (default).

.. confval:: maxitems
   :name: category-maxitems
   :required: false
   :type: integer
   :default: "0"

   Maximum number of items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems
   :name: category-minitems
   :required: false
   :type: integer

   Minimum number of items. Default is no minimum. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting :yaml:`minitems` to at least 1.

.. confval:: treeConfig.startingPoints
   :name: category-treeConfig.startingPoints
   :required: false
   :type: string

   Allows to set one or more roots (category uids), from which the categories
   should be taken from.

.. confval:: default
   :name: category-default
   :required: false
   :type: string
   :default: ''

   Default value set if a new record is created. If empty, no category gets
   selected.

.. confval:: exclusiveKeys
   :name: category-exclusiveKeys
   :required: false
   :type: string
   :default: ''

   Comma separated list of item values which exclude any other selection once
   chosen. See :ref:`exclusiveKeys <t3tca:columns-category-properties-exclusivekeys>`.

.. confval:: foreign_table_where
   :name: category-foreign_table_where
   :required: false
   :type: string
   :default: ''

   Additional :sql:`WHERE` clause used to fetch the selectable categories. It
   replaces the default constraint, so repeat the default when narrowing it
   further. See
   :ref:`foreign_table_where <t3tca:columns-category-properties-foreign-table-where>`.

.. confval:: readOnly
   :name: category-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: category-size
   :required: false
   :type: integer
   :default: 20

   Maximal number of elements to be displayed in the tree by default. Minimum is
   :yaml:`5`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/category
    fields:
      - identifier: categories
        type: Category

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/category
    fields:
      - identifier: categories
        type: Category
        minitems: 1
        treeConfig:
          startingPoints: 7
        relationship: oneToOne
