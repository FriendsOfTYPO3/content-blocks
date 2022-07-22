.. include:: /Includes.rst.txt
.. _field_type_collection:

==========
Collection
==========

The "Collection" type generates a field for Inline-Relational-Record-Editing
(IRRE), which allows nesting of other field types as children.
This field type allows building structures like image sliders, where properties
beyond the image meta fields are required per child item.

It corresponds with the TCA `type='inline'`.


Properties
==========

.. rst-class:: dl-parameters

collapseAll
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'true'
   :sep:`|`

   Show all child-records collapsed (if false, all are expanded)

enabledControls
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` 'true'
   :sep:`|`

   Associative array with the keys ‘info’, ‘new’, ‘dragdrop’, ‘sort’, ‘hide’,
   ‘delete’, ‘localize’. If the accordant values are set to a boolean value
   (true or false), the control is shown or hidden in the header of each record.

enableSorting
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'true'
   :sep:`|`

   Activate drag & drop.

expandSingle
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'true'
   :sep:`|`

   Show only one child-record expanded each time. If a collapsed record is clicked,
   the currently open one collapses and the clicked one expands.

fields
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
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

maxItems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

minItems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.

useAsLabel
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Defines which field of the collection item should be used as the title of the
   inline element. The given field has to be a string based field type, or at
   least be convertable to a string.

   Example:

   .. code-block:: yaml

      useAsLabel: text
      fields:
        - identifier: text
          type: Text
        - identifier: image
          type: Image

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: slides
        type: Collection
        properties:
          collapseAll: true
          enabledControls:
            delete: true
            dragdrop: true
            new: true
            hide: true
            info: true
            localize: true
          enableSorting: true
          expandSingle: true
          fields:
            - identifier: image
              type: Image
              properties:
                minItems: 1
                maxItems: 1
                required:  true
            - identifier: title
              type: Text
          maxItems: 5
          minItems: 1
          useAsLabel: title
