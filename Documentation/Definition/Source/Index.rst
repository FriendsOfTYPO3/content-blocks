.. include:: /Includes.rst.txt
.. _cb_definition_source:

======
Source
======

The **Source** folder contains private resources. If you are familiar with the
directory structure of extensions, this would be the **Resources/Private**
folder. There is a limited set of directories and files, which you can place
here.

EditorPreview.html
==================

This file is only available for :ref:`Content Elements <yaml_reference_content_element>`.

The **EditorPreview.html** can be added to customize the backend preview for
your editors. By default, TYPO3 comes with a standard preview renderer. However,
it is specialized in rendering the preview of Core Content Elements. This means
only Core fields like :sql:`header`, :sql:`subheader` or :sql:`bodytext` are
considered. Therefore, it is advised to provide an own preview for custom
Content Elements.

Learn more about :ref:`templating <cb_templating>`.

Frontend.html
=============

This is the default frontend rendering definition for :ref:`Content Elements <yaml_reference_content_element>`.
You can access your fields by the variable :html:`{data}`.

Learn more about :ref:`templating <cb_templating>`.

Partials
========

For larger Content Elements, you can divide your **Frontend.html** template into
smaller chunks by creating separate partials here.

Partials are included as you normally would in any Fluid template.

.. code-block:: html

   <f:render partial="Component.html" arguments="{_all}"/>

See also:

*  Learn how to :ref:`share Partials <cb_extension_partials>` between Content Blocks.
