.. include:: /Includes.rst.txt
.. _field_type_select-number:

============
SelectNumber
============

The :yaml:`SelectNumber` type generates a simple select field, which only allows
numbers / integers.

Settings
========

..  confval-menu::
    :name: confval-select-number-options
    :display: table
    :type:
    :default:
    :required:

..  confval:: default
    :name: select-number-default
    :required: false
    :type: integer

   Default value set if a new record is created.

..  confval:: items
    :name: select-number-items
    :required: false
    :type: array

   Contains the elements for the selector box. Each item is an array. An item
   consists of a :yaml:`label` and a :yaml:`value`.

   Example:

   .. code-block:: yaml

      items:
        - label: 'The first'
          value: 1
        - label: 'The second'
          value: 2
        - label: 'The third'
          value: 3

   .. tip::

      You can omit the label, if you have the translation already in your
      labels.xlf file.

      .. code-block:: yaml

          items:
            - value: 1
            - value: 2
            - value: 3

   .. tip::

      You can also use icons so they are displayed in the backend.
      See :ref:`select-number-icons` for a full example.

      .. code-block:: yaml

          items:
            - value: 1
              icon: content-beside-text-img-left
            - value: 2
              icon: content-beside-text-img-right
            - value: 3
              icon: content-beside-text-img-above-center

      For this you need the following setting according to the :ref:`TCA documentation <t3tca:tca_property_fieldWizard_selectIcons>`.

      .. code-block:: yaml

          fieldWizard:
            selectIcons:
              disabled: false

   XLF translation keys for items have the following convention:

   .. code-block:: xml

        <body>
            <trans-unit id="FIELD_IDENTIFIER.items.1.label">
                <source>Label for item with value one</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.2.label">
                <source>Label for item with value two</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.VALUE.label">
                <source>Label for item with value VALUE</source>
            </trans-unit>
        </body>


Example
=======

Minimal
-------

..  code-block:: yaml

    name: example/select-number
    fields:
      - identifier: select_number
        type: SelectNumber
        items:
          - label: 'The first'
            value: 1
          - label: 'The second'
            value: 2

Advanced / use case
-------------------

..  _select-number-icons:

Select with icons:

..  code-block:: yaml

    name: example/select-number
    fields:
     - identifier: select_number_icons
       type: SelectNumber
       fieldWizard:
         selectIcons:
           disabled: false
       default: 2
       items:
         - label: 'Image beside text (left)'
           value: 1
           icon: content-beside-text-img-left
         - label: 'Image beside text (right)'
           value: 2
           icon: content-beside-text-img-right
         - label: 'Image above text (center)'
           value: 3
           icon: content-beside-text-img-above-cent
