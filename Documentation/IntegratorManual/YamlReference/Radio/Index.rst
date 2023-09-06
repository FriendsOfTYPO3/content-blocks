.. include:: /Includes.rst.txt
.. _field_type_radio:

=====
Radio
=====

The `Radio` type generates a number of radio fields.

It corresponds with the TCA :php:`type => 'radio'`.

SQL overrides via :yaml:`alternativeSql` allowed: yes.

Settings
========

.. confval:: default

   :Required: false
   :Type: string
   :Default: ''

   Default value set if a new record is created.

.. confval:: items

   :Required: true
   :Type: array

   Contains the checkbox elements. Each item is an array with the first being
   the label in the select drop-down (LLL reference possible) and the second
   being the value transferred to the input field.

   Example:

   .. code-block:: yaml

      items:
        [
          [ 'one', 'one' ],
          [ 'two', 'two' ],
          [ 'three', 'three' ],
        ]

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-radio>`.

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/radio
    group: common
    fields:
      - identifier: radioboxes
        type: Radio
        items:
          [
            [ 'one', 'one' ],
            [ 'two', 'two' ],
          ]

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/radio
    group: common
    fields:
      - identifier: radioboxes
        type: Radio
        default: one
        items:
          - label: 'The first'
            value: one
          - label: 'The second'
            value: two
          - label: 'The third'
            value: three
