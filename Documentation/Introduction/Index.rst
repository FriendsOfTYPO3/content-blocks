.. include:: /Includes.rst.txt
.. _introduction:

============
Introduction
============

   **Motivation:**

   Defining "Content Elements" in TYPO3 is hard and the learning curve is steep.

The concept of a so called `Content Block` is introduced in the form of a new
system extension `content_blocks` into the Core. A Content Block is a simplified
way to define new Content Types. This includes `Content Elements`, `Page Types`
and generic `Record Types`. The definition is declared mainly inside a YAML
file. This YAML file defines both, the required fields and the structure in
which they are displayed in the editing view in the backend. From this single
file all other "low-level" definitions are generated during the runtime and
stored in a dedicated cache. Content Blocks is not there (and not able) to
replace the current system, but adds another abstraction layer to a complicated
API.

For more insights about what Content Blocks replaces, read :ref:`this appendix <core-content-types>`.

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

Learn more a

EditorInterface.yaml
^^^^^^^^^^^^^^^^^^^^

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

Fluid templating
^^^^^^^^^^^^^^^^

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
