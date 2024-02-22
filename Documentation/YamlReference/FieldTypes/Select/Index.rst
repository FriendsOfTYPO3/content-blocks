.. include:: /Includes.rst.txt
.. _field_type_select:

======
Select
======

:php:`type => 'select' // TCA`

The :yaml:`Select` type generates a simple select field.

Settings
========

.. confval:: renderType

   :Required: yes
   :Type: string

   *  :yaml:`selectSingle`
   *  :yaml:`selectCheckBox`
   *  :yaml:`selectSingleBox`
   *  :yaml:`selectTree`
   *  :yaml:`selectMultipleSideBySide`

.. confval:: items

   :Required: yes
   :Type: array

   Contains the elements for the selector box. Each item is an array. An item
   consists of a :yaml:`label` and a :yaml:`value`.

   Example:

   .. code-block:: yaml

      items:
        - label: 'The first'
          value: one
        - label: 'The second'
          value: two
        - label: 'The third'
          value: three

   .. tip::

      You can omit the label, if you have the translation already in your
      Labels.xlf file.

      .. code-block:: yaml

          items:
            - value: one
            - value: two
            - value: three

   XLF translation keys for items have the following convention:

   .. code-block:: xml

        <body>
            <trans-unit id="FIELD_IDENTIFIER.items.one.label">
                <source>Label for item with value one</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.two.label">
                <source>Label for item with value two</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.VALUE.label">
                <source>Label for item with value VALUE</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.label">
                <source>Label for item with empty value</source>
            </trans-unit>
        </body>

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

.. include:: /Snippets/AllowedCustomProperties.rst

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-select>`.

Example
=======

Minimal
-------

Select single:

.. code-block:: yaml

    name: example/select
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
    fields:
      - identifier: select_tree
        type: Select
        renderType: selectTree
        size: 5
        foreign_table: 'pages'
        foreign_table_where: 'ORDER BY pages.uid'
        treeConfig:
          parentField: pid
