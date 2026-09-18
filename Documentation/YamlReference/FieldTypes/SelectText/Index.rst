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


..  confval:: authMode
    :name: select-text-authMode
    :required: false
    :type: string
    :default: ''

    Authorization mode for the selector box. The only allowed value is
    :yaml:`explicitAllow`: every static item has to be explicitly granted to a
    backend user group before a non-admin user may select it.

..  confval:: disableNoMatchingValueElement
    :name: select-text-disableNoMatchingValueElement
    :required: false
    :type: boolean
    :default: false

    If set, no placeholder element is inserted when the stored value does not
    match any of the configured items.

..  confval:: itemGroups
    :name: select-text-itemGroups
    :required: false
    :type: array
    :default: []

    Key-value pairs which define groups for the items. The key is the group
    identifier referenced by an item, the value is the group label. Groups are
    rendered as :html:`<optgroup>`.

    .. code-block:: yaml

       itemGroups:
         colors: 'Colors'
         shapes: 'Shapes'

..  confval:: readOnly
    :name: select-text-readOnly
    :required: false
    :type: boolean
    :default: false

    Renders the field in a way that the user can see the value but cannot edit it.

..  confval:: size
    :name: select-text-size
    :required: false
    :type: integer
    :default: 1

    If set to :yaml:`1` (default), a drop-down is displayed, else a select box of
    the given size.

..  confval:: sortItems
    :name: select-text-sortItems
    :required: false
    :type: array
    :default: []

    Sort order of the select items. Allowed keys are :yaml:`label` and
    :yaml:`value`, each with :yaml:`asc` or :yaml:`desc`.

    .. code-block:: yaml

       sortItems:
         label: asc

..  confval:: dbFieldLength
    :name: select-text-dbFieldLength
    :required: false
    :type: integer
    :default: 255

    Length of the generated :sql:`varchar` database column.

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
