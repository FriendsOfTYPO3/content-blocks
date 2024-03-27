.. include:: /Includes.rst.txt
.. _field_type_datetime:

========
DateTime
========

:php:`type => 'datetime' // TCA`

The :yaml:`DateTime` type provides a date picker. If not configured otherwise,
the value is stored as a timestamp.

Settings
========

.. confval:: default
   :name: datetime-default

   :Required: false
   :Type: string
   :Default: ''

   Default value in `Y-m-d` format. Set if a new record is created.
   For example :yaml:`2023-01-01`.

.. confval:: format
   :name: datetime-format

   :Required: false
   :Type: string
   :Default: ''

   Defines how the date should be formatted in the backend. Possible values are
   `datetime`, `date` or `time` and `timesec`.

.. confval:: dbType
   :name: datetime-dbType

   :Required: false
   :Type: string
   :Default: ''

   This option changes the date field to a native MySql :sql:`DATETIME`,
   :sql:`DATE` or :sql:`TIME` field. Possible values are `datetime`, `date`
   or `time` respectively.

.. confval:: range
   :name: datetime-range

   :Required: false
   :Type: array

   An array which defines an integer range within which the value must be. Keys:

   lower (string in format `H:i Y-m-d`)
      Defines the min date.

   upper (string in format `H:i Y-m-d`)
      Defines the max date.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: '2020-01-01'
        upper: '2020-12-31'

.. confval:: disableAgeDisplay
   :name: datetime-disableAgeDisplay

   :Required: false
   :Type: boolean
   :Default: false

   Disable the display of the age in the backend view.

.. confval:: required
   :name: datetime-required

   :Required: false
   :Type: boolean
   :Default: false

   If set, the field becomes mandatory.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-datetime>`.

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
