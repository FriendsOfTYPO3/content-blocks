.. include:: /Includes.rst.txt
.. _field_type_radio:

=====
Radio
=====

:php:`type => 'radio' // TCA`
:yaml:`alternativeSql: true`

The :yaml:`Radio` type creates a set of radio buttons. The value is typically
stored as integer value, each radio item has one assigned number, but it can be
a string, too.

Settings
========

.. confval:: default

   :Required: false
   :Type: string|int
   :Default: ''

   Default value set if a new record is created.

.. confval:: items

   :Required: true
   :Type: array

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

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-radio>`.

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
