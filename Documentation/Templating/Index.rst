.. include:: /Includes.rst.txt

.. _cb_templating:

==========
Templating
==========

The following examples are for templating with Fluid.

Content Blocks brings some additional features like own variables and
ViewHelpers with it.

Accessing variables
===================

Inside your `Frontend.html` or `EditorPreview.html` file you can access the
properties of your Content Element as usual by the :html:`{data}` variable.
This variable, however, is special. It has real superpowers. Let's have a look
at the debug output of it:

.. code-block:: text

    TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockData [prototype] [object]
       _raw => [private] array(85 items)
       _processed => [private] array(8 items)
          uid => 24 (integer)
          pid => 1 (integer)
          languageId => 0 (integer)
          typeName => 'example_element1' (16 chars)
          updateDate => 1694625077 (integer)
          creationDate => 1694602137 (integer)
          header => 'Foo' (3 chars)

As you can see, in contrast to the usual array, we are dealing with an object
here. This allows us to magically access our own custom properties very easily.
The object consists of two properties `_raw` and `_processed`. As the names
suggest, the one is raw and unprocessed and the other one has magic applied from
Content Blocks. Normally you would access the processed properties. This is done
by simply accessing the desired property like :html:`{data.header}`. Note, that
we are omitting `_processed` here. This is important to remember, as this would
access a custom field named `_processed`. On the other hand, the raw properties
have to be accessed by :html:`{data._raw.some_field}`. But most of the time you
shouldn't need them.

All fields with relations are resolved automatically to an array. This includes
`Collection`, `Select`, `Relation`, `File`, `Folder`, `Category` and `FlexForm`
fields. There is no need to provide additional DataProcessors for them.
Content Blocks applies relation resolving for you (recursively!).

Have a look at this code example to grasp what's possible:

.. code-block:: html

    <!-- Normal access to custom properties -->
    {data.my_field}

    <!-- Normal access to custom relational properties -->
    <f:for each="{data.collection1}" as="item">{item.title}</f:for>

    <!-- Recursive access to custom relational properties -->
    <f:for each="{data.collection1}" as="item">
        <f:for each="{item.categories}" as="category">
            {category.title}
        </f:for>
    </f:for>

    <!-- There are some special accessors, which are always available: -->
    {data.uid}
    {data.pid}
    {data.typeName} <!-- This is the CType for Content Elements -->

    <!-- These special accessors are available, if the corresponding features are turned on (Always true for Content Elements) -->
    {data.languageId} <!-- YAML: languageAware: true -->
    {data.creationDate} <!-- YAML: trackCreationDate: true -->
    {data.updateDate} <!-- YAML: trackUpdateDate: true -->

    <!-- These special accessors are available depending on the context -->
    {data.localizedUid}
    {data.originalUid}
    {data.originalPid}

    <!-- To access the raw (unprocessed) database record use `_raw` -->
    {data._raw.some_field}

Frontend & backend
==================

The Content Blocks allow you to provide a separate template for the frontend and
the backend out of the box. The variables are the same for both templates, and
while using the asset ViewHelpers, you can also ship JavaScript and CSS as you
need. The main goal behind this is, that you can provide a better user
experience for the editors. With this feature, there is the possibility to
provide nearly the same layout in the frontend and the backend, so the editors
easily find the element they want to edit.

The frontend template is located in `Source/Frontend.html` and the backend
template in `Source/EditorPreview.html`.

ViewHelper & assets
===================

Since Content Blocks are stored in an extra path structure, accessing assets
(JavaScript and CSS) can lead to complicated paths. So the well known
AssetCollector with his related ViewHelpers will work, but it might be very
complicated to use. Content Blocks provides new ViewHelpers to access assets
from the related Content Block of a template. This asset ViewHelpers look for
the given file in the `Assets` directory of the Content Block.

Example for a CSS file:

.. code-block:: html

    <cb:asset.css identifier="myCssIdentifier" file="Frontend.css"/>


Example for a JavaScript file:

.. code-block:: html

    <cb:asset.script identifier="myJavascriptIdentifier" file="Frontend.js"/>


The mapping between the assets and the Content Block in the ViewHelper is done
by the :html:`{data}` object which is set automatically. But if you try to use
an asset ViewHelper in e.g. a partial, you have to ship :html:`{data}` to the
partial, or you can set :html:`name` by hand:

.. code-block:: html

    <cb:asset.script identifier="myJavascriptIdentifier" name="vendor/content-block-name" file="Frontend.js"/>


ViewHelper & translation
========================

Analogous to the asset ViewHelpers, there is also a ViewHelper for translations.
This ViewHelper looks directly in the `Labels.xlf` file for the given key.

.. code-block:: html

    <cb:translate key="my.contentblock.header" />

As described above in the asset ViewHelper, the mapping between the Content
Block and the translation file is done by the :html:`{data}` variable in the
Fluid template of a Content Block. You can also set :html:`name` by hand:

.. code-block:: html

    <cb:translate key="my.contentblock.header" name="vendor/content-block-name" />

Partials
========

Partials are a very useful feature of Fluid. You can use them to split up your
templates into smaller parts. If you want to use a partial in a Content Block,
you can create a subdirectory `Partials` in the `Source` directory and place
your partials there.

This part is automatically added, but you can also extend or overwrite this
TypoScript configuration in your sitepackage.

Remember, that you should ship the :html:`{data}` variable to the partial if you
want to use the asset or translation ViewHelpers within.

Layouts
=======

Analogous to partials, you can also use layouts. You can create a subdirectory
`Layouts` in the `Source` directory and place your layouts there. The
configuration is added automatically, but you can also extend or overwrite the
TypoScript configuration in your sitepackage. Afterwards you can use your
layouts as usual in Fluid.

Shareable resources
===================

There is the technical possibility to use resources from the whole TYPO3 setup
(e.g. translations, scripts, or partials from other extensions), but we do not
recommend to do so. Since the Content Blocks are intended to be easily copied
and pasted between different projects, your Content Block might break and you
lose this initial benefit.
