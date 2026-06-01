.. include:: /Includes.rst.txt
.. _field_type_checkbox:

========
Checkbox
========

The :yaml:`Checkbox` type generates one or more checkbox fields.

Settings
========

..  confval-menu::
    :name: confval-checkbox-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: checkbox-default
   :required: false
   :type: integer (bit value)
   :default: "0"

   The default value corresponds to a bit value. If you only have one checkbox
   having 1 or 0 will work to turn it on or off by default. For more than one
   checkbox you need to calculate the bit representation.

.. confval:: items
   :name: checkbox-items
   :required: false
   :type: array

   Only necessary if more than one checkbox is desired. Contains the checkbox
   elements as separate array items. The `label` can also be defined as a
   LLL-reference.

   Example:

   .. code-block:: yaml

      items:
        - label: 'The first'
        - label: 'The second'
        - label: 'The third'

   XLF translation keys for items have the following convention:

   .. code-block:: xml

        <body>
            <trans-unit id="FIELD_IDENTIFIER.items.0.label">
                <source>Label for first Checkbox item</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.1.label">
                <source>Label for second Checkbox item</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.n.label">
                <source>Label for nth Checkbox item</source>
            </trans-unit>
        </body>

   The :yaml:`renderType: checkboxLabeledToggle` additionally has :yaml:`labelChecked`
   and :yaml:`labelUnchecked`.

   .. code-block:: yaml
      renderType: checkboxLabeledToggle
      items:
        - label: 'Normal Label'
          labelChecked: 'Label for when checked'
          labelUnchecked: 'Label for when unchecked'

   XLF translation keys for labelChecked/Unchecked have the following convention:

   .. code-block:: xml

        <body>
            <trans-unit id="FIELD_IDENTIFIER.items.0.labelChecked">
                <source>Label for when checked</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.0.labelUnchecked">
                <source>Label for when unchecked</source>
            </trans-unit>
        </body>

.. confval:: renderType
   :name: checkbox-renderType
   :required: false
   :type: string
   :default: check

   *  :yaml:`checkboxToggle`
   *  :yaml:`checkboxLabeledToggle`

.. confval:: allowedCustomProperties
   :name: checkbox-allowedCustomProperties
   :required: false
   :type: array
   :default: ["itemsProcConfig"]

   Sometimes it is needed to provide custom configuration for the :ref:`itemsProcFunc <t3tca:tca_property_itemsProcFunc>`
   functionality. These extra properties need to be explicitly allowed via this
   option. This option receives an array of those strings. By default, the
   custom option :yaml:`itemsProcConfig` is allowed.

.. confval:: behaviour.allowLanguageSynchronization
   :name: checkbox-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: cols
   :name: checkbox-cols
   :required: false
   :type: integer|string

   Defines in how many columns the checkboxes are shown. Only meaningful when
   multiple :yaml:`items` are defined. Accepts an integer between 1 and 31, or
   the string :yaml:`inline`.

.. confval:: eval
   :name: checkbox-eval
   :required: false
   :type: string

   Configuration of field evaluation. Possible values:

   *  :yaml:`maximumRecordsChecked`
   *  :yaml:`maximumRecordsCheckedInPid`
   *  :yaml:`maximumRecordsChecked,maximumRecordsCheckedInPid`

   Use together with :yaml:`validation` to set the maximum.

.. confval:: fieldControl
   :name: checkbox-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: checkbox-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: checkbox-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: invertStateDisplay
   :name: checkbox-invertStateDisplay
   :required: false
   :type: boolean

   A checkbox is marked checked if the database bit is not set and vice versa.
   Applies to all checkboxes. Can also be set per item inside :yaml:`items`.

.. confval:: itemsProcessors
   :name: checkbox-itemsProcessors
   :required: false
   :type: object

   A list of PHP classes called to fill or manipulate the items array. Each
   entry is keyed by a numeric index and requires a :yaml:`class` property.
   An optional :yaml:`parameters` object can be passed to the processor.

   Example:

   .. code-block:: yaml

      itemsProcessors:
        0:
          class: 'Vendor\Extension\ItemsProcessor\MyProcessor'
          parameters:
            foo: bar

.. confval:: itemsProcFunc
   :name: checkbox-itemsProcFunc
   :required: false
   :type: string

   .. deprecated:: 2.3.0

      Use :yaml:`itemsProcessors` instead.

   PHP method which is called to fill or manipulate the items array. See
   :ref:`TCA itemsProcFunc <t3tca:tca_property_itemsProcFunc>`.

.. confval:: itemsProcConfig
   :name: checkbox-itemsProcConfig
   :required: false
   :type: object

   Additional configuration passed to :yaml:`itemsProcFunc`. Must be listed in
   :yaml:`allowedCustomProperties` (included by default).

.. confval:: readOnly
   :name: checkbox-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: validation.maximumRecordsChecked
   :name: checkbox-validation.maximumRecordsChecked
   :required: false
   :type: integer

   Maximum number of records that can have this checkbox checked system-wide.
   Requires :yaml:`eval: maximumRecordsChecked`.

.. confval:: validation.maximumRecordsCheckedInPid
   :name: checkbox-validation.maximumRecordsCheckedInPid
   :required: false
   :type: integer

   Maximum number of records that can have this checkbox checked within a page.
   Requires :yaml:`eval: maximumRecordsCheckedInPid`.


Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/checkbox
    fields:
      - identifier: checkbox
        type: Checkbox

Advanced / use case
-------------------

Multiple checkboxes:

.. code-block:: yaml

    name: example/checkbox
    fields:
      - identifier: checkbox
        type: Checkbox
        items:
          - label: 'The first'
          - label: 'The second'
          - label: 'The third'
        default: 2
        cols: 3

Toggle checkbox:

.. code-block:: yaml

    name: example/checkbox
    fields:
      - identifier: toggle
        type: Checkbox
        renderType: checkboxToggle
        default: 1

Labeled toggle checkbox:

.. code-block:: yaml

    name: example/checkbox
    fields:
      - identifier: toggle
        type: Checkbox
        renderType: checkboxLabeledToggle
        items:
          - label: 'Your label'
            labelChecked: 'Label checked'
            labelUnchecked: 'Label unchecked'
            invertStateDisplay: true
