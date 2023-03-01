.. include:: /Includes.rst.txt
.. _field_type_select:

======
Select
======

The `Select` type generates a simple select field.

It corresponds with the TCA :php:`type => 'select'`.

SQL overrides via `alternativeSql` allowed: yes.

Properties
==========

.. rst-class:: dl-parameters

renderType
   :sep:`|` :aspect:`Required:` yes
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

   Choose from one of the four available select types: :yaml:`selectSingle`,
   :yaml:`selectCheckBox`, :yaml:`selectSingleBox` or
   :yaml:`selectMultipleSideBySide`.

items
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|`

   Contains the elements for the selector box. Each item is an array with the first being
   the label in the select drop-down (LLL reference possible) and the second
   being the value transferred to the input field.

   Example:

   .. code-block:: yaml

      items:
        [
          [ 'The first', 'one' ],
          [ 'The second', 'two' ],
          [ 'The third', 'three' ],
        ]

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

maxitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.
   If `maxitems` ist set to greater than 1, multiselect is automatically enabled.

minitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-select>`.

Example
=======

Select single:

.. code-block:: yaml

    group: common
    fields:
      - identifier: select
        type: Select
        properties:
          renderType: selectSingle
          default: 'one'
          items:
            [
              [ 'The first', 'one' ],
              [ 'The second', 'two' ],
              [ 'The third', 'three' ],
            ]

Select multiple:

.. code-block:: yaml

    group: common
    fields:
      - identifier: selectSideBySide
        type: Select
        properties:
          renderType: selectMultipleSideBySide
          default: 'one'
          items:
            [
              [ 'The first', 'one' ],
              [ 'The second', 'two' ],
              [ 'The third', 'three' ],
            ]
          maxitems: 2
          minitems: 1
