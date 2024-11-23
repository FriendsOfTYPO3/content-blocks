.. include:: /Includes.rst.txt
.. _yaml_reference_content_element:

===============
ContentElements
===============

Folder: `ContentBlocks/ContentElements`

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/cta/config.yaml

    name: example/cta
    fields:
      - identifier: header
        useExistingField: true

Learn more about :ref:`Content Elements <api_content_elements>`.

Options
=======

Here you can find all :ref:`common root options <yaml_reference_common>`.

..  confval-menu::
    :name: confval-content-element-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: description
   :name: content-element-description
   :required: false
   :type: string

   This is the description of the Content Element. If you have a labels.xlf
   file, you should define it there with the key :xml:`description`. If both are
   defined, the translation file has precedence.

   .. code-block:: yaml

       description: "Here comes my description"

.. confval:: group
   :name: content-element-group
   :required: false
   :type: string
   :default: default

   The group is used for the grouping of the record type selector in the edit
   view of records. In addition, it is used for the "New Content Element Wizard"
   for the tab grouping. By default, all new types are placed in the `default`
   group.

   .. code-block:: yaml

       group: special

   The Core defines these groups for Content Elements:

   *  `default`
   *  `menu`
   *  `special`
   *  `forms`
   *  `plugins`

.. confval:: typeName
   :name: content-element-typeName
   :required: false
   :type: string
   :default: automatically generated from :yaml:`name`

   The identifier of the new Content Element. It is automatically generated from
   the name, if not defined manually.

   .. code-block:: yaml

       typeName: my_content_element

.. confval:: saveAndClose
   :name: content-element-saveAndClose
   :required: false
   :type: bool
   :default: false

   Can be activated in order to skip the edit view when adding the Content
   Element via the NewContentElementWizard. This can be useful if you have a
   Content Element or Plugin without configuration.

   .. code-block:: yaml

       saveAndClose: true
