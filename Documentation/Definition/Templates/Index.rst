.. include:: /Includes.rst.txt
.. _cb_definition_source:

=========
templates
=========

The **templates** folder contains private resources. If you are familiar with the
directory structure of extensions, this would be the **Resources/Private**
folder. There is a limited set of directories and files, which you can place
here.

backend-preview.fluid.html
==========================

The **backend-preview.fluid.html** can be added to customize the backend preview for
your editors.

Learn more about :ref:`backend previews <api_backend_preview>`.

frontend.fluid.html
===================

This is the default frontend rendering definition for :ref:`Content Elements <yaml_reference_content_element>`.
You can access your fields by the variable :html:`{data}`.

Learn more about :ref:`templating <cb_templating>`.

partials
========

For larger Content Elements, you can divide your **frontend.fluid.html** template into
smaller chunks by creating separate partials here.

Partials are included as you normally would in any Fluid template.

.. note::

   Due to current Fluid restrictions, partials have to start with an uppercase
   letter. This restriction might be lifted in later Fluid versions (v5 or above).


.. code-block:: html

   <f:render partial="Component" arguments="{_all}"/>

See also:

*  Learn how to :ref:`share partials <cb_extension_partials>` between Content Blocks.

layouts
=======

You can also add layouts to your Content Block if needed.

.. code-block:: html

   <f:layout name="MyLayout">
