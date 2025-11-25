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

.. confval:: searchable
   :name: datetime-searchable
   :required: false
   :type: boolean
   :default: true

   If set to false, the field will not be considered in backend search.

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
