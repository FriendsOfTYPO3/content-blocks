.. include:: /Includes.rst.txt
.. _field_type_collection:

==========
Collection
==========

The `Collection` type generates a field for Inline-Relational-Record-Editing
(IRRE), which allows nesting of other field types as children.
This field type allows building structures like image sliders, accordion, tabs
and so on.

It corresponds with the TCA :php:`type => 'inline'`.

SQL overrides via `alternativeSql` allowed: no.

First-level options
===================

.. rst-class:: dl-parameters

useAsLabel
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Defines which field of the collection item should be used as the title of the
   inline element. If not defined, the first child field will be used as the
   label.

   Example:

   .. code-block:: yaml

      identifier: collection
      type: Collection
      useAsLabel: text
      properties:
          fields:
            - identifier: image
              type: Image
            - identifier: text
              type: Text

Properties
==========

.. rst-class:: dl-parameters

maxitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

minitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.

fields
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` []
   :sep:`|`

   Configures a set of fields as repeatable child objects. All fields defined in
   field types are possible as children. However, consider not to have
   too many nested Collection fields to avoid performance issues. Content Blocks
   are not intended to represent complex data structures. Consider to create
   custom tables for these cases.

   Example:

   .. code-block:: yaml

      fields:
        - identifier: text
          type: Text
        - identifier: image
          type: Image

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-inline>`

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/collection
    group: common
    fields:
      - identifier: collection
        type: Collection
        properties:
          fields:
            - identifier: text
              type: Text

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/collection
    group: common
    fields:
      - identifier: slides
        type: Collection
        useAsLabel: title
        properties:
          maxitems: 5
          minitems: 1
          fields:
            - identifier: image
              type: Image
              properties:
                minitems: 1
                maxitems: 1
                required: true
            - identifier: title
              type: Text
