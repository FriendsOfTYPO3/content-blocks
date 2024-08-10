.. include:: /Includes.rst.txt

.. _cb_templating:

==========
Templating
==========

The following examples are for templating with Fluid. Content Blocks brings some
additional features like own variables and ViewHelpers with it.

Accessing variables
===================

Inside your `Frontend.html` or `EditorPreview.html` file you can access the
properties of your Content Element as usual by the :html:`{data}` variable.
This variable, however, is special. It has real superpowers!

In contrast to the usual array, we are dealing with an object here. This allows
us to magically access our own custom properties very easily. Normally you would
access the processed properties. This is done by simply accessing the desired
property like :html:`{data.header}`. The raw properties have to be accessed by
:html:`{data._raw.some_field}`. But most of the time you shouldn't need them.

All fields with relations are resolved automatically to an array. This includes
`Collection`, `Select`, `Relation`, `File`, `Folder`, `Category` and `FlexForm`
fields. There is no need to provide additional DataProcessors for them.
Content Blocks applies relation resolving for you (recursively!).

Have a look at this code example to grasp what's possible:

.. code-block:: html

    <!-- Any property, which is available in the Record (like normal) -->
    {data.title}
    {data.uid}
    {data.pid}

    <!-- Normal access to custom relational properties -->
    <f:for each="{data.collection1}" as="item">{item.title}</f:for>

    <!-- Recursive access to custom relational properties -->
    <f:for each="{data.collection1}" as="item">
        <f:for each="{item.categories}" as="category">
            {category.title}
        </f:for>
    </f:for>

    <!-- Language related properties -->
    {data.languageId}
    {data.languageInfo.translationParent}
    {data.languageInfo.translationSource}

    <!-- The overlaid uid -->
    {data.overlaidUid}

    <!-- Types are a combination of the table name and the Content Type name. -->
    <!-- Example for table "tt_content" and CType "textpic": -->

    <!-- "tt_content" (this is basically the table name) -->
    {data.mainType}

    <!-- "textpic" (this is the CType) -->
    {data.recordType}

    <!-- "tt_content.textpic" (Combination of mainType and record type, separated by a dot) -->
    {data.fullType}

    <!-- System related properties -->
    {data.systemProperties.deleted}
    {data.systemProperties.disabled}
    {data.systemProperties.lockedForEditing}
    {data.systemProperties.createdAt}
    {data.systemProperties.lastUpdatedAt}
    {data.systemProperties.publishAt}
    {data.systemProperties.publishUntil}
    {data.systemProperties.userGroupRestriction}
    {data.systemProperties.sorting}
    {data.systemProperties.description}

    <!-- Computed properties depending on the request context -->
    {data.computedProperties.versionedUid}
    {data.computedProperties.localizedUid}
    {data.computedProperties.requestedOverlayLanguageId}
    {data.computedProperties.translationSource} <!-- Only for pages, contains the Page model -->

    <!-- Workspace related properties -->
    {data.versionInfo.workspaceId}
    {data.versionInfo.liveId}
    {data.versionInfo.state.name}
    {data.versionInfo.state.value}
    {data.versionInfo.stageId}

    <!-- To access the raw (unprocessed) database record use `_raw` -->
    {data._raw.some_field}

See also: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/13.2/Feature-103783-RecordTransformationDataProcessor.html#usage-in-fluid-templates

.. note::

   Note that we are omitting `_processed` when accessing properties, even
   though you might think this would be correct due to the debug output.

Frontend & backend
==================

Content Blocks allows you to provide a separate template for the frontend and
the backend out of the box. The variables are the same for both templates, and
while using the asset ViewHelpers, you can also ship JavaScript and CSS as you
need. The main goal behind this is, that you can provide a better user
experience for the editors. With this feature, there is the possibility to
provide nearly the same layout in the frontend and the backend, so the editors
easily find the element they want to edit.

The frontend template is located in **Source/Frontend.html** and the backend
template in **Source/EditorPreview.html**.

.. _asset_view_helpers:

Asset ViewHelpers
=================

Content Blocks provides a new AssetPathViewHelper to access assets from within
the current Content Block in the template. These ViewHelpers look for the given
file in the `Assets` directory.

.. code-block:: html

    <f:comment><!-- Include the Assets/Frontend.css stylesheet --></f:comment>
    <f:asset.css identifier="myCssIdentifier" file="{cb:assetPath()}/Frontend.css"/>

    <f:comment><!-- Include the Assets/Frontend.js script --></f:comment>
    <f:asset.script identifier="myJavascriptIdentifier" file="{cb:assetPath()}/Frontend.js"/>

The information of the current Content Block is stored in :html:`{data}`. This
means if you use an asset ViewHelper in a partial, you have to provide
:html:`{data}` as an argument to that partial. Alternatively, you can set
:html:`name` by hand:

.. code-block:: html

    <f:comment><!-- The name of the Content Block is set explicitly --></f:comment>
    <f:asset.script identifier="myJavascriptIdentifier" name="vendor/name" file="{cb:assetPath()}/Frontend.js"/>

Translation ViewHelper
======================

This ViewHelper looks directly in the `Labels.xlf` file for the given key.

.. code-block:: html

    <cb:translate key="my.contentblock.header" />

As described above in the asset ViewHelper, the :html:`{data}` variable is
required to resolve the Content Block automatically. You can also set
:html:`name` by hand:

.. code-block:: html

    <cb:translate key="my.contentblock.header" name="vendor/name" />

Partials
========

Partials are a very useful feature of Fluid. You can use them to split up your
templates into smaller parts. If you want to use a partial in a Content Block,
you can create a subdirectory **Partials** in the **Source** directory and place
your partials there.

This part is automatically added, but you can also
:ref:`extend or overwrite <cb_extension_partials>` this TypoScript configuration
in your sitepackage.

Remember, that you should ship the :html:`{data}` variable to the partial if you
want to make use of automatic detection of the current Content Block.

.. code-block:: html

   <f:render partial="Component.html" arguments="{data: data, foo: 'bar'}"/>

See also:

*  :ref:`Shared partials <cb_extension_partials>`

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
recommend to do so. Content Blocks are intended to work independent of external
resources so they can be easily copy-pasted between projects. Be aware of this
downside, when you add dependencies to your Content Block.
