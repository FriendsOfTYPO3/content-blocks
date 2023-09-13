.. include:: /Includes.rst.txt
.. _field_type_select:

======
Select
======

The `Select` type generates a simple select field.

It corresponds with the TCA :php:`type => 'select'`.

SQL overrides via `alternativeSql` allowed: yes.

Settings
========

.. confval:: renderType

   :Required: yes
   :Type: string

   Choose from one of the available select types: :yaml:`selectSingle`,
   :yaml:`selectCheckBox`, :yaml:`selectSingleBox`, :yaml:`selectTree` or
   :yaml:`selectMultipleSideBySide`.

.. confval:: items

   :Required: yes
   :Type: array

   Contains the elements for the selector box. Each item is an array with the first being
   the label in the select drop-down (LLL reference possible) and the second
   being the value transferred to the input field.

   Example:

   .. code-block:: yaml

      items:
        - label: 'The first'
          value: one
        - label: 'The second'
          value: two
        - label: 'The third'
          value: three

.. confval:: default

   :Required: false
   :Type: string

   Default value set if a new record is created.

.. confval:: maxitems

   :Required: false
   :Type: integer

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.
   If `maxitems` ist set to greater than 1, multiselect is automatically enabled.

.. confval:: minitems

   :Required: false
   :Type: integer

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-select>`.

Example
=======

Minimal
-------

Select single:

.. code-block:: yaml

    name: example/select
    group: common
    fields:
      - identifier: select
        type: Select
        renderType: selectSingle
        items:
          - label: 'The first'
            value: one
          - label: 'The second'
            value: two

Select multiple:

.. code-block:: yaml

    name: example/select
    group: common
    fields:
      - identifier: select_side_by_side
        type: Select
        renderType: selectMultipleSideBySide
        items:
          - label: 'The first'
            value: one
          - label: 'The second'
            value: two

Advanced / use case
-------------------

Select single:

.. code-block:: yaml

    name: example/select
    group: common
    fields:
      - identifier: select
        type: Select
        renderType: selectSingle
        default: 'one'
        minitems: 1
        maxitems: 3
        items:
        - label: 'The first'
          value: one
        - label: 'The second'
          value: two
        - label: 'The third'
          value: three
        foreign_table: pages
        foreign_table_where: 'AND {#pages}.{#pid} = 123 ORDER BY uid'

Select multiple:

.. code-block:: yaml

    name: example/select
    group: common
    fields:
      - identifier: select_side_by_side
        type: Select
        renderType: selectMultipleSideBySide
        default: 'one'
        minitems: 1
        maxitems: 3
        items:
        - label: 'The first'
          value: one
        - label: 'The second'
          value: two
        - label: 'The third'
          value: three
        foreign_table: pages
        foreign_table_where: 'AND {#pages}.{#pid} = 123 ORDER BY uid'

Select tree:

.. code-block:: yaml

    name: example/select
    group: common
    fields:
      - identifier: select_tree
        type: Select
        renderType: selectTree
        size: 5
        foreign_table: 'pages'
        foreign_table_where: 'ORDER BY pages.uid'
        treeConfig:
        parentField: pid
