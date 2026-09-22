.. include:: /Includes.rst.txt
.. _content_blocks_view_helper_asset_path:

============
cb:assetPath
============

.. rst-class:: horizbuttons-attention-m

*  Class: :php:`TYPO3\CMS\ContentBlocks\ViewHelpers\AssetPathViewHelper`

Resolves the public path to the :file:`assets` directory of a Content Block.
Use it together with the Core :html:`f:asset.css` and :html:`f:asset.script`
ViewHelpers to include CSS and JavaScript files, which are shipped inside the
Content Block itself.

.. warning::

    This ViewHelper only works inside the template of a Content Block. It
    resolves the current Content Block via the :html:`data._name` or
    :html:`settings._content_block_name` template variable. If neither
    variable is available (for example when called from a partial rendered
    completely outside of a Content Block context) and :html:`name` is not
    given explicitly, an exception is thrown.

Arguments
=========

..  confval-menu::
    :name: confval-asset-path-arguments
    :display: table
    :type:
    :default:
    :required:

.. confval:: name
   :name: asset-path-name
   :required: false
   :type: string

   The target Content Block name (:yaml:`vendor/name`). If not set, the
   current Content Block is resolved automatically.

Examples
========

Default usage
--------------

Inside the current Content Block's own template, :html:`name` can be omitted.
The current Content Block is detected automatically.

.. code-block:: html

    <f:asset.css identifier="myCssIdentifier" href="{cb:assetPath()}/frontend.css"/>
    <f:asset.script identifier="myJavascriptIdentifier" src="{cb:assetPath()}/frontend.js"/>

Explicit Content Block name
----------------------------

You can also point to the assets of a different Content Block by setting
:html:`name` explicitly.

.. code-block:: html

    <f:asset.script identifier="myJavascriptIdentifier" src="{cb:assetPath(name: 'vendor/name')}/frontend.js"/>

See also
========

*  :ref:`Asset ViewHelpers <asset_view_helpers>`
*  :ref:`Assets definition <cb_definition_assets>`
