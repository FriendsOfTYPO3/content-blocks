.. include:: /Includes.rst.txt
.. _field_type_datetime:

========
DateTime
========

The :yaml:`DateTime` type provides a date picker. If not configured otherwise,
the value is stored as a timestamp.

Settings
========

..  confval-menu::
    :name: confval-datetime-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: datetime-default
   :required: false
   :type: string
   :default: ''

   Default value in `Y-m-d` format. Set if a new record is created.
   For example :yaml:`2023-01-01`.

.. confval:: format
   :name: datetime-format
   :required: false
   :type: string
   :default: ''

   Defines how the date should be formatted in the backend. Possible values are
   `datetime`, `date` or `time` and `timesec`.

.. confval:: dbType
   :name: datetime-dbType
   :required: false
   :type: string
   :default: ''

   This option changes the date field to a native MySql :sql:`DATETIME`,
   :sql:`DATE` or :sql:`TIME` field. Possible values are `datetime`, `date`
   or `time` respectively.

.. confval:: range
   :name: datetime-range
   :required: false
   :type: array

   An array which defines an integer range within which the value must be. Keys:

   lower (string in format `Y-m-d H:i:s`)
      Defines the min date.

   upper (string in format `Y-m-d H:i:s`)
      Defines the max date.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: '2020-01-01'
        upper: '2020-12-31'

.. confval:: disableAgeDisplay
   :name: datetime-disableAgeDisplay
   :required: false
   :type: boolean
   :default: false

   Disable the display of the age in the backend view.

.. confval:: required
   :name: datetime-required
   :required: false
   :type: boolean
   :default: false

   If set, the field becomes mandatory.

.. confval:: behaviour.allowLanguageSynchronization
   :name: datetime-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: fieldControl
   :name: datetime-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: datetime-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: datetime-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: mode
   :name: datetime-mode
   :required: false
   :type: string

   When set to :yaml:`useOrOverridePlaceholder`, a checkbox appears above the
   field allowing the user to override the placeholder value.

.. confval:: nullable
   :name: datetime-nullable
   :required: false
   :type: boolean
   :default: false

   Allows the database field to store a :sql:`NULL` value.

.. confval:: placeholder
   :name: datetime-placeholder
   :required: false
   :type: string

   Placeholder text displayed inside the field when it is empty.

.. confval:: readOnly
   :name: datetime-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/datetime
    fields:
      - identifier: datetime
        type: DateTime
        format: date

.. code-block:: yaml

    name: example/datetime
    fields:
      - identifier: datetime
        type: DateTime
        format: datetime
        dbType: datetime

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/datetime
    fields:
      - identifier: datetime
        type: DateTime
        format: datetime
        default: '2023-02-11 12:00:00'
        disableAgeDisplay: true
        size: 20
        range:
          lower: '2019-01-31 12:00:00'
          upper: '2040-01-31 12:00:00'
        required: true
