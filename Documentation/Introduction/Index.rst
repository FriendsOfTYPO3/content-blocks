.. include:: /Includes.rst.txt
.. _introduction:

============
Introduction
============

   **Motivation:**

   Defining "Content Elements" in TYPO3 is hard and the learning curve is steep.

A Content Block is a simplified way to define new Content Types. This includes
`Content Elements`, `Page Types` and generic `Record Types`. The definition is
declared mainly inside a YAML file. This YAML file defines both, the required
fields and the structure in which they are displayed in the editing view in
the backend. From this single file all other "low-level" definitions are
generated. Content Blocks is not there to replace the current system, but adds
another, optional abstraction layer to a, for historic reasons, complicated API.

For more technical / historical / conceptual insights about Content Blocks we
recommend this further readings:

*  :ref:`Defining Content Types the Core way <core-content-types>`
*  :ref:`History <cb_history>`

Structure
=========

To create a new Content Block, a folder `ContentBlocks` has to be created
on the root level inside an existing and loaded extension. Then, depending on
the Content Type you want to create, you either create a `ContentElements`,
`PageTypes` or `RecordTypes` folder, in which you finally put your Content
Block inside. To quickly kickstart a new Content Block, the command
:bash:`make:content-block` can be used.

A minimal Content Block consists of this directory structure:

.. code-block:: text
   :caption: EXT:some_extension/ContentBlocks/ContentElements/my_content_block/

    Assets/
        Icon.svg
    Source/
        Language/
            Labels.xlf
        EditorPreview.html
        Frontend.html
    EditorInterface.yaml

The `Assets` folder can be compared with the `Resources/Public` folder and the
`Source` folder with `Resources/Private`. The `Assets/Icon.svg` is registered
automatically for the Content Type icon. Only Content Elements can have a
`EditorPreview.html` and `Frontend.html` file. The backend translations are put
inside `Source/Language/Labels.xlf`.

*  Learn more about the the :ref:`Content Block definition <cb_definition>`.
*  Kickstart a Content Block with the :ref:`skeleton builder <cb_skeleton>`.

EditorInterface.yaml
====================

The heart of a Content Block is the `EditorInterface.yaml` file. This YAML file
defines both the available fields and the structure:

.. code-block:: yaml

    name: vendor/content-block-name
    fields:
      - identifier: header
        useExistingField: true
      - identifier: my_text_field
        type: Text
        max: 10

First of all, a :yaml:`name` has to be defined. It must be unique inside your
installation. It consists, similarly to composer package names, of a vendor and
a package part separated by a slash. It is used to prefix new field names, new
tables and record type identifiers.

Inside :yaml:`fields` you define the structure and configuration of the
necessary fields. The :yaml:`identifier` has to be unique per Content Block.

It is possible to reuse existing fields with the flag :yaml:`useExistingField`.
This allows e.g. to use the same field `header` or `bodytext` across multiple
Content Blocks with different configuration. Be aware that system fields
shouldn't be reused. A list of sane reusable fields can be referenced in the
documentation. Of course, own custom fields can be reused as well.

*  Refer to the :ref:`YAML reference <yaml_reference>` for a complete overview.
*  Learn more about :ref:`reusing fields <cb_reuse_existing_fields>`.
*  Learn how to :ref:`extend TCA <cb_extendTca>` of Content Blocks.

Fluid templating
================

The `EditorPreview.html` is the Fluid template for the backend preview and the
`Frontend.html` template for the frontend. Both contain the above defined fields
inside the variable :html:`data` and can be directly accessed.

.. code-block:: html

    <cb:asset.css identifier="content-block-foo" file="Frontend.css"/>
    <cb:asset.script identifier="content-block-foo" file="Frontend.js"/>
    <cb:translate key="my-key"/>

    My header: {data.header}
    My textfield: {data.my_text_field}

Content Blocks provides its own asset ViewHelpers :html:`<cb:asset.css>` and
:html:`<cb:asset.script>`. Required arguments are :html:`identifier`,
and :html:`file` (relative to the "Assets" folder inside the Content Block).
Be aware: the Core asset ViewHelpers won't work for Content Blocks in composer
mode.

For frontend translations Content Blocks also provides its own translation
ViewHelper. This can be seen as a simplified :html:`f:translate` ViewHelper.
The only required argument is :html:`key`. The ViewHelper will automatically
resolve the path to the `Labels.xlf` file of the current Content Block.

*  Here is the main article for :ref:`templating with Content Blocks <cb_templating>`.
*  Learn how to :ref:`share Partials <cb_extension_partials>` between Content Blocks.
