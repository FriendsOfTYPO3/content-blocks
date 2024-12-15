.. include:: /Includes.rst.txt
.. _api_file_types:

==========
File Types
==========

.. versionadded:: 1.2

For YAML reference refer to :ref:`this page <yaml_reference_file_types>`.

File Types are a special Content Type in TYPO3. They relate to the field type
:ref:`file <field-type-file>`. Every time you create a new file reference, the
type of the child record is automatically determined by the file mime type.

There is a finite amount of types:

*  text
*  image
*  audio
*  video
*  application

TYPO3 already provides a basic palette of fields, including an image
manipulation module to handle crop areas. Now, if you happen to need an
additional custom field, you have to completely override the type definition. A
minimal example looks like this:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/FileTypes/image/config.yaml

    name: example/image
    typeName: image
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

In this example, we re-define the :yaml:`image` file type. For the most part
everything is identical to the standard definition. We only added another custom
field :yaml:`example_custom_field` right before the crop field. Using this
method, you have full control over the available fields and their position.

..  note::
    This is a global definition, used in your entire TYPO3 installation. Only
    use it, if you want to modify the arrangement of fields globally. Also,
    don't use this in third party extensions. Better provide a :yaml:`Basic`,
    which the extension user should include in his project.

..  note::
    If you have the option :ref:`extendedPalette <confval-file-extendedpalette>`
    set to :yaml:`false`, this definition won't be displayed and you will get
    the basic palette instead.

..  figure:: /API/_Images/file-type-image-palette.jpg
    :alt: File reference in the TYPO3 backend
