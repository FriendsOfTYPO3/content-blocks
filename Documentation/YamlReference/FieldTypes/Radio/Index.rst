.. include:: /Includes.rst.txt
.. _field_type_radio:

=====
Radio
=====

The :yaml:`Radio` type creates a set of radio buttons. The value is typically
stored as integer value, each radio item has one assigned number, but it can be
a string, too.

Settings
========

..  confval-menu::
    :name: confval-radio-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: radio-default
   :required: false
   :type: string|int
   :default: ''

   Default value set if a new record is created.

.. confval:: items
   :name: radio-items
   :required: true
   :type: array

   Contains the radio items. Each item is an array with the keys :yaml:`label`
   and :yaml:`value`. Values are usually integers, but can also be strings if
   desired.

   Example:

   .. code-block:: yaml

      items:
        - label: 'First option'
          value: 0
        - label: 'Second option'
          value: 1
        - label: 'Third option'
          value: 2


   XLF translation keys for items have the following convention:

   .. code-block:: xml

        <body>
            <trans-unit id="FIELD_IDENTIFIER.items.1.label">
                <source>Label for item with value 1</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.2.label">
                <source>Label for item with value 2</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.VALUE.label">
                <source>Label for item with value VALUE</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.label">
                <source>Label for item with empty value</source>
            </trans-unit>
        </body>

.. confval:: allowedCustomProperties
   :name: radio-allowedCustomProperties

   :required: false
   :type: array
   :default: ["itemsProcConfig"]

   Sometimes it is needed to provide custom configuration for the :ref:`itemsProcFunc <t3tca:tca_property_itemsProcFunc>`
   functionality. These extra properties need to be explicitly allowed via this
   option. This option receives an array of those strings. By default, the
   custom option :yaml:`itemsProcConfig` is allowed.

.. confval:: behaviour.allowLanguageSynchronization
   :name: radio-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: fieldControl
   :name: radio-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: radio-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: radio-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: itemsProcFunc
   :name: radio-itemsProcFunc
   :required: false
   :type: string

   PHP method which is called to fill or manipulate the items array. See
   :ref:`TCA itemsProcFunc <t3tca:tca_property_itemsProcFunc>`.

.. confval:: itemsProcConfig
   :name: radio-itemsProcConfig
   :required: false
   :type: object

   Additional configuration passed to :yaml:`itemsProcFunc`. Must be listed in
   :yaml:`allowedCustomProperties` (included by default).

.. confval:: readOnly
   :name: radio-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/radio
    fields:
      - identifier: radioboxes
        type: Radio
        items:
          - label: 'First option'
            value: 0
          - label: 'Second option'
            value: 1

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/radio
    fields:
      - identifier: radioboxes
        type: Radio
        default: 'one'
        items:
          - label: 'First option'
            value: 'one'
          - label: 'Second option'
            value: 'two'
          - label: 'Third option'
            value: 'three'
