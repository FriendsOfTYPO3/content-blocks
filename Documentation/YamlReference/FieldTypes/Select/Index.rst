.. include:: /Includes.rst.txt
.. _field_type_select:

======
Select
======

The :yaml:`Select` type generates a simple select field.

Settings
========

..  confval-menu::
    :name: confval-select-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: renderType
   :name: select-renderType
   :required: yes
   :type: string

   *  :yaml:`selectSingle`
   *  :yaml:`selectCheckBox`
   *  :yaml:`selectSingleBox`
   *  :yaml:`selectTree`
   *  :yaml:`selectMultipleSideBySide`

.. confval:: items
   :name: select-items
   :required: false
   :type: array

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
      labels.xlf file.

      .. code-block:: yaml

          items:
            - value: one
            - value: two
            - value: three

   .. tip::

      You can also use icons so they are displayed in the backend.
      See :ref:`select-icons` for a full example.

      .. code-block:: yaml

          items:
            - value: image-left
              icon: content-beside-text-img-left
            - value: image-right
              icon: content-beside-text-img-right
            - value: image-above
              icon: content-beside-text-img-above-center

      For this you need the following setting according to the :ref:`TCA documentation <t3tca:tca_property_fieldWizard_selectIcons>`.

      .. code-block:: yaml

          fieldWizard:
            selectIcons:
              disabled: false

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
   :name: select-default
   :required: false
   :type: string

   Default value set if a new record is created.

.. confval:: maxitems
   :name: select-maxitems
   :required: false
   :type: integer

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.
   If `maxitems` ist set to greater than 1, multiselect is automatically enabled.

.. confval:: minitems
   :name: select-minitems
   :required: false
   :type: integer

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

.. confval:: relationship
   :name: select-relationship
   :required: false
   :type: string
   :default: oneToMany

   .. note::

      This can only be used in combination with :yaml:`foreign_table`.

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), :yaml:`manyToOne` and
   :yaml:`oneToOne`. In case of a [x]toOne relation, the processed field will
   be filled directly with the record instead of a collection of records. In
   addition, :yaml:`maxitems` will be automatically set to :yaml:`1`. If the
   :yaml:`renderType` is set to :yaml:`selectSingle`, a relationship
   :yaml:`manyToOne` is automatically inferred.

.. confval:: dbFieldLength
   :name: select-dbFieldLength
   :required: false
   :type: int
   :default: 255

   This option can be used to set an alternative size for the database
   :sql:`varchar` column. The default size is `255`.

.. confval:: allowedCustomProperties
   :name: select-allowedCustomProperties
   :required: false
   :type: array
   :default: ["itemsProcConfig"]

   Sometimes it is needed to provide custom configuration for the :ref:`itemsProcFunc <t3tca:tca_property_itemsProcFunc>`
   functionality. These extra properties need to be explicitly allowed via this
   option. This option receives an array of those strings. By default, the
   custom option :yaml:`itemsProcConfig` is allowed.

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


..  _select-icons:

Select with icons:

.. code-block:: yaml

   name: example/select
   fields:
     - identifier: select_icons
       type: Select
       renderType: selectSingle
       fieldWizard:
         selectIcons:
           disabled: false
       default: 'image-left'
       items:
         - label: 'Image beside text (left)'
           value: image-left
           icon: content-beside-text-img-left
         - label: 'Image beside text (right)'
           value: image-right
           icon: content-beside-text-img-right
         - label: 'Image above text (center)'
           value: image-above
           icon: content-beside-text-img-above-cent
