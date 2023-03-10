.. include:: /Includes.rst.txt
.. _field_type_checkbox:
.. rst-class:: dl-parameters

========
Checkbox
========

The `Checkbox` type generates one or more checkbox fields.

It corresponds with the TCA :php:`type => 'check'`.

SQL overrides via :yaml:`alternativeSql` allowed: yes.

Properties
==========

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer (bit value)
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   The default value corresponds to a bit value. If you only have one checkbox
   having 1 or 0 will work to turn it on or off by default. For more than one
   checkbox you need to calculate the bit representation.

items
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Only necessary if more than one checkbox is desired. Contains the checkbox
   elements as separate array items. The label can also be defined as a
   LLL-reference.

   Example:

   .. code-block:: yaml

      items:
        [
          [ 'The first' ],
          [ 'The second' ],
          [ 'The third' ],
        ]

For advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-check>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    group: common
    fields:
      - identifier: checkbox
        type: Checkbox

Advanced / use case
-------------------

Multiple checkboxes:

.. code-block:: yaml

    group: common
    fields:
      - identifier: checkbox
        type: Checkbox
        properties:
          items:
            [
              [ 'The first' ],
              [ 'The second' ],
              [ 'The third' ],
            ]
          default: 2
          cols: 3

Toggle checkbox:

.. code-block:: yaml

    group: common
    fields:
      - identifier: toggle
        type: Checkbox
        properties:
          renderType: checkboxToggle
          default: 1
