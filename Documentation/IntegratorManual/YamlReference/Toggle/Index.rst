.. include:: /Includes.rst.txt
.. _field_type_toggle:

======
Toggle
======

The "Toggle" type generates a number of checkbox fields with toggles.

It corresponds with the TCA `type='check'` and `renderType='checkboxToggle'`.


Properties
==========

.. rst-class:: dl-parameters

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

items
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Contains the toggle elements. Each item is an array with the first being
   the value transferred to the input field, and the second being the label in
   the select drop-down (LLL reference possible).

   Example:

   .. code-block:: yaml

      items:
        'one': 'The first'
        'two': 'The second'
        'three': 'The third'

invertStateDisplay
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Inverts the display state (onf/off) of the toggle items.

   Example:

   .. code-block:: yaml

      invertStateDisplay: true

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: toggleInverted
        type: Toggle
        properties:
          default: 'one'
          items:
            'one': 'The first'
            'two': 'The second'
            'three': 'The third'
          invertStateDisplay: true
