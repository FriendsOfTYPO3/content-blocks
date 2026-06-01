.. include:: /Includes.rst.txt
.. _field_type_file:

====
File
====

The :yaml:`File` type generates a field for file relations.

Settings
========

..  confval-menu::
    :name: confval-file-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: extendedPalette
   :name: file-extendedPalette
   :required: false
   :type: boolean
   :default: true

   If enabled, an additional image or media palette will be rendered. For
   image files it consists of the additional fields `crop`, `alternative` and
   `link`. For audio and media files an additional `autoplay` field is added.
   For other file types, like plain text, this option has no effect. Disable
   this option, if you don't need these additional fields.

.. confval:: allowed
   :name: file-allowed
   :required: false
   :type: string|array
   :default: ''

    Possible values: `common-image-types`, `common-media-types` or your custom
    list of file types.

.. confval:: maxitems
   :name: file-maxitems
   :required: false
   :type: integer
   :default: 99999

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems
   :name: file-minitems
   :required: false
   :type: integer

   Minimum number of items. Default is no minimum. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting :yaml:`minitems` to at least 1.

.. confval:: relationship
   :name: file-relationship
   :required: false
   :type: string
   :default: oneToMany

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), or :yaml:`oneToOne`. In case of a
   oneToOne relation, the processed field will be filled directly with the file
   reference instead of a collection of file references. In addition,
   :yaml:`maxitems` will be automatically set to :yaml:`1`.

.. confval:: cropVariants
   :name: file-cropVariants
   :required: false
   :type: array
   :default: []

   It is possible to define crop variants for this specific field and Content
   Block. This documentation only covers the most basic configuration. Refer to
   the :ref:`TCA documentation <t3tca:columns-imageManipulation-properties-cropVariants>`
   for a complete overview of possibilities.

   Example configuration below. The aspect ratios can be defined as a float
   value or a fraction. Only the simple division operation `a / b` is allowed.

   .. code-block:: yaml

    cropVariants:
      default:
        title: Teaser
        allowedAspectRatios:
          portrait:
            title: Portrait
            value: 0.75
          landscape:
            title: Landscape
            value: 4 / 3

.. confval:: overrideType
   :name: file-overrideType
   :required: false
   :type: array
   :default: []

   Type Overrides can be used to override the File Definition in the context of
   as single field. Refer to the :ref:`API documentation <api_type_overrides>`
   if you want to learn more.

   .. code-block:: yaml

      overrideType:
        image:
          - identifier: image_overlay_palette
            type: Palette
            label: 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette'
            fields:
              - identifier: alternative
                useExistingField: true
              - identifier: description
                useExistingField: true
              - type: Linebreak
              - identifier: link
                useExistingField: true
              - identifier: title
                useExistingField: true
              - type: Linebreak
              - identifier: example_custom_field
                type: Text
                label: 'My custom Field'
              - type: Linebreak
              - identifier: crop
                useExistingField: true

.. confval:: appearance.collapseAll
   :name: file-appearance.collapseAll
   :required: false
   :type: boolean

   When true, all file records are shown collapsed. When false, all are
   expanded.

.. confval:: appearance.expandSingle
   :name: file-appearance.expandSingle
   :required: false
   :type: boolean

   When enabled, only one file record is expanded at a time.

.. confval:: appearance.createNewRelationLinkTitle
   :name: file-appearance.createNewRelationLinkTitle
   :required: false
   :type: string

   Overrides the "Create new relation" button label with a localized string.

.. confval:: appearance.addMediaLinkTitle
   :name: file-appearance.addMediaLinkTitle
   :required: false
   :type: string

   Overrides the "Add media by URL" button label with a localized string.

.. confval:: appearance.uploadFilesLinkTitle
   :name: file-appearance.uploadFilesLinkTitle
   :required: false
   :type: string

   Overrides the "Select & upload files" button label with a localized string.

.. confval:: appearance.useSortable
   :name: file-appearance.useSortable
   :required: false
   :type: boolean
   :default: true

   Activates drag & drop sorting of file records.

.. confval:: appearance.showPossibleLocalizationRecords
   :name: file-appearance.showPossibleLocalizationRecords
   :required: false
   :type: boolean

   Show unlocalized file records that exist in the original language but have
   not yet been translated.

.. confval:: appearance.showAllLocalizationLink
   :name: file-appearance.showAllLocalizationLink
   :required: false
   :type: boolean

   Show a "Localize all records" link to fetch untranslated records from the
   original language.

.. confval:: appearance.showSynchronizationLink
   :name: file-appearance.showSynchronizationLink
   :required: false
   :type: boolean

   Show a "Synchronize" link to update to a 1:1 translation with the original
   language.

.. confval:: appearance.enabledControls
   :name: file-appearance.enabledControls
   :required: false
   :type: object

   Enables or disables individual controls on file records. Available keys
   with their defaults:

   edit (bool, default true)
      Show or hide the edit control.

   info (bool, default true)
      Show or hide the info control.

   dragdrop (bool, default true)
      Show or hide the drag & drop handle.

   sort (bool, default false)
      Show or hide the sort arrows.

   hide (bool, default true)
      Show or hide the hide/show toggle.

   delete (bool, default true)
      Show or hide the delete control.

   localize (bool, default true)
      Show or hide the localize control.

   Example:

   .. code-block:: yaml

      appearance:
        enabledControls:
          sort: true
          delete: false

.. confval:: appearance.headerThumbnail
   :name: file-appearance.headerThumbnail
   :required: false
   :type: object

   Defines the dimensions of the preview thumbnail shown in the inline header.
   Accepts :yaml:`width` and :yaml:`height` as string or integer values.

   Example:

   .. code-block:: yaml

      appearance:
        headerThumbnail:
          width: 64
          height: 64

.. confval:: appearance.fileUploadAllowed
   :name: file-appearance.fileUploadAllowed
   :required: false
   :type: boolean
   :default: true

   Show or hide the "Select & upload file" button.

.. confval:: appearance.fileByUrlAllowed
   :name: file-appearance.fileByUrlAllowed
   :required: false
   :type: boolean

   Show or hide the "Add media by URL" button, used to embed media from
   services such as YouTube or Vimeo.

.. confval:: appearance.elementBrowserEnabled
   :name: file-appearance.elementBrowserEnabled
   :required: false
   :type: boolean

   Show or hide the element browser button.

.. confval:: behaviour.allowLanguageSynchronization
   :name: file-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: behaviour.disableMovingChildrenWithParent
   :name: file-behaviour.disableMovingChildrenWithParent
   :required: false
   :type: boolean
   :default: false

   Disables automatic moving of file references when the parent record is
   moved.

.. confval:: behaviour.enableCascadingDelete
   :name: file-behaviour.enableCascadingDelete
   :required: false
   :type: boolean
   :default: true

   When disabled, attached file references are not deleted when the parent
   record is deleted.

.. confval:: disallowed
   :name: file-disallowed
   :required: false
   :type: string|array

   File extensions to disallow even if they are listed in :yaml:`allowed`.
   Accepts the same values as :yaml:`allowed`.

.. confval:: fieldInformation
   :name: file-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: file-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: overrideChildTca
   :name: file-overrideChildTca
   :required: false
   :type: object

   Overrides TCA configuration of the :sql:`sys_file_reference` records
   attached to this field.

.. confval:: readOnly
   :name: file-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

Example
=======

Minimal
-------

All file types allowed, no restrictions.

.. code-block:: yaml

    name: example/file
    fields:
      - identifier: my_file_field
        type: File

Advanced / use case
-------------------

Allow only image types, disable extended palette (no cropping field), require
at least one image and set limit to 10 images.

.. code-block:: yaml

    name: example/image
    fields:
      - identifier: image
        type: File
        extendedPalette: false
        minitems: 1
        maxitems: 10
        allowed: common-image-types

Allow media types like audio, video and youtube (or vimeo).

.. code-block:: yaml

    name: example/media
    fields:
      - identifier: media
        type: File
        allowed: common-media-types

Set specific crop variants for an image field.

.. code-block:: yaml

    name: example/image
    fields:
      - identifier: image
        type: File
        allowed: common-image-types
        cropVariants:
          desktop:
            title: Desktop
            allowedAspectRatios:
              portrait:
                title: Portrait
                value: 0.75
              landscape:
                title: Landscape
                value: 4 / 3
            focusArea:
              x: 0.3
              y: 0.3
              width: 0.4
              height: 0.4
            coverAreas:
              - x: 0.1
                y: 0.8
                width: 0.8
                height: 0.1
          tablet:
            title: Tablet
            allowedAspectRatios:
              square:
                title: Square
                value: 0.75
          smartphone:
            title: Smartphone
            allowedAspectRatios:
              landscape:
                title: Landscape
                value: 4 / 3

Usage in Fluid
==============

.. code-block:: html

    <f:for each="{data.image}" as="image">
        <f:image image="{image}" width="120" maxHeight="100"/>
    </f:for>
