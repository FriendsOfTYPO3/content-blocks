.. include:: /Includes.rst.txt
.. _field_type_select-text:

==========
SelectText
==========

The :yaml:`SelectText` type generates a simple select field, which only allows
simple text.

Settings
========

..  confval-menu::
    :name: confval-select-text-options
    :display: table
    :type:
    :default:
    :required:

..  confval:: default
    :name: select-text-default
    :required: false
    :type: string

   Default value set if a new record is created.

..  confval:: items
    :name: select-text-items
    :required: false
    :type: array

   Contains the elements for the selector box. Each item is an array. An item
   consists of a :yaml:`label` and a :yaml:`value`.

   Example:

   .. code-block:: yaml

      items:
        - label: 'The first'
          value: 'first'
        - label: 'The second'
          value: 'second'
        - label: 'The third'
          value: 'third'

   .. tip::

      You can omit the label, if you have the translation already in your
      labels.xlf file.

      .. code-block:: yaml

          items:
            - value: 'first'
            - value: 'second'
            - value: 'third'

   .. tip::

      You can also use icons so they are displayed in the backend.
      See :ref:`select-text-icons` for a full example.

      .. code-block:: yaml

          items:
            - value: 'first'
              icon: content-beside-text-img-left
            - value: 'second'
              icon: content-beside-text-img-right
            - value: 'third'
              icon: content-beside-text-img-above-center

      For this you need the following setting according to the :ref:`TCA documentation <t3tca:tca_property_fieldWizard_selectIcons>`.

      .. code-block:: yaml

          fieldWizard:
            selectIcons:
              disabled: false

   XLF translation keys for items have the following convention:

   .. code-block:: xml

        <body>
            <trans-unit id="FIELD_IDENTIFIER.items.first.label">
                <source>Label for item with value one</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.second.label">
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

    name: example/select-text
    fields:
      - identifier: select_text
        type: SelectText
        items:
          - label: 'The first'
            value: 'first'
          - label: 'The second'
            value: 'second'

Advanced / use case
-------------------

..  _select-text-icons:

Select with icons:

..  code-block:: yaml

    name: example/select-text
    fields:
     - identifier: select_text_icons
       type: SelectText
       fieldWizard:
         selectIcons:
           disabled: false
       default: 'text-left'
       items:
         - label: 'Image beside text (left)'
           value: 'text-left'
           icon: content-beside-text-img-left
         - label: 'Image beside text (right)'
           value: 'text-right'
           icon: content-beside-text-img-right
         - label: 'Image above text (center)'
           value: 'text-center'
           icon: content-beside-text-img-above-cent
