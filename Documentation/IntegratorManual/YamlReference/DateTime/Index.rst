.. include:: /Includes.rst.txt
.. _field_type_datetime:

========
DateTime
========

The `DateTime` type provides a date picker. If not configured otherwise, the
value is stored as a timestamp.

It corresponds with the TCA `type => 'datetime'`.

SQL overrides via `alternativeSql` allowed: no.

Properties
==========

.. rst-class:: dl-parameters

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value in `Y-m-d` format. Set if a new record is created.
   For example :yaml:`2023-01-01`.

dbType
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   This option changes the date field to a native MySql :sql:`DATE`,
   :sql:`DATETIME` or :sql:`TIME` field. Possible values are `datetime`, `date`
   or `time`.

range
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   An array which defines an integer range within which the value must be. Keys:

   lower (string in format `H:i Y-m-d`)
      Defines the lower integer value. Default: 0.

   upper (string in format `H:i Y-m-d`)
      Defines the upper integer value. Default: 100.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: '2020-01-01'
        upper: '2020-12-31'

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-datetime>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    group: common
    fields:
      - identifier: datetime
        type: DateTime

.. code-block:: yaml

    group: common
    fields:
      - identifier: datetime
        type: DateTime
        dbType: datetime

Advanced / use case
-------------------

.. code-block:: yaml

    group: common
    fields:
      - identifier: datetime
        type: DateTime
        properties:
          default: '2023-02-11 12:00:00'
          displayAge: true
          size: 20
          range:
            lower: '2019-01-31 12:00:00'
            upper: '2040-01-31 12:00:00'
          required: true
