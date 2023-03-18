.. include:: /Includes.rst.txt

.. _cb_templating:

===============================
Templating with Content Blocks
===============================

The Content Blocks are mainly used in combination with Fluid Template Engine, but
you can also use them in other template engines. The following examples are for templating
with Fluid.

The Content Blocks bring some additional features like own variables and ViewHelpers with them.


Accessing variables
===================

There are all variables available which you are used to in Fluid. E.g. `{data.uid}` or something like
that will work as expected. Withing a Content Block there is a new area, where you can find the data
from the database related to a Content Block. This is the `cb` variable. So if you want to access the identifier `myTextField` of a field in the
root level of a Content Block, you can use `{cb.myTextField}`.

One big advantage of this is, that e.g. images, files and collections can simply accessed and processed as
an array. You do not need any additional DataProcessor for that.

In this example, you can see how to access a field of type File, which is intented to be an image:

.. code-block:: html

    <f:for each="{cb.myImage}" as="image">
        <f:image src="{image.uid}" />
    </f:for>



ViewHelper & assets
===================

Since Content Blocks are stored in an extra path structure, accessing assets (JavaScript and CSS) can lead to complicated paths.
So the well known AssetCollector with his related ViewHelpers will work, but it might be very complicated to use. The Content Blocks
provide new ViewHelpers to access assets from the related Content Block of a template. This asset ViewHelpers looking for the given
file in the 'Asset' directory of the Content Block.

Example for a CSS file:

.. code-block:: html

    <cb:asset.css identifier="myCssIdentifier" file="Frontend.css"/>


Example for a JavaScript file:


.. code-block:: html

    <cb:asset.script identifier="myJavascriptIdentifier" file="Frontend.js"/>


The mapping between the assets and the Content Block in the ViewHelper is done by the TypoScript configuration of a Content Block
in `tt_content.content_block_identifier.settings.name = vendor/package` which is set automatically. But if you try to use a
asset ViewHelper in e.g. a partial, you have to ship the `settings` to the partial, or you can set the `name` attribute by hand:

.. code-block:: html

    <cb:asset.script identifier="myJavascriptIdentifier" name="vendor/package" file="Frontend.js"/>


ViewHelper & translation
========================

Analogue to the asset ViewHelpers, there is also a ViewHelper for translations. This ViewHelper is looking directly in the `Labels.xlf`
file for the given key.

.. code-block:: html

    <cb:translate key="my.contentblock.header" />

As described above in the asset ViewHelper, the mapping between the Content Block and the translation file is done by the TypoScript
configuration of a Content Block in `tt_content.content_block_identifier.settings.name = vendor/package` which is set automatically.
But if you try to use a translation ViewHelper in e.g. a partial, you have to ship the `settings` to the partial, or you can set the
`name` attribute by hand:

.. code-block:: html

    <cb:translate key="my.contentblock.header" name="vendor/package" />


Partials
========

Partials are a very useful feature of Fluid. You can use them to split up your templates into smaller parts. If you want to use a partial
in a Content Block, you can create a subdirectory `Partials` in the `Source` directory and dump your partials there.

This part is automatically added, but you can also extend or overwrite this TypoScript configuration in your sitepackage.

Remember, that you should ship the `settings` to the partial if you want to use the asset or translation ViewHelpers within.


Layouts
=======

Analogue to the partials, you can also use layouts. You can create a subdirectory `Layouts` in the `Source` directory and dump your
layouts there. This part is automatically added, but you can also extend or overwrite this TypoScript configuration in your sitepackage.
Afterwards you can use your layouts as usual in Fluid.


Shareable resources
====================

Despite there is the technical possibility to use resources from the whole TYPO3 setup (e.g. translations, scripts, or partials from other extensions),
we do not recommend to do so. Since the Content Blocks are intended to be easily copied and pasted between different projects, your Content Block might
get broken and you loose this initial benefit.
