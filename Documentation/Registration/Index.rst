.. include:: /Includes.rst.txt
.. _cb_installation:

============
Registration
============

In order to register a new Content Block, a folder **ContentBlocks** has to be
created on the root level inside a loaded extension. Depending on the Content
Type you want to create, choose one of these folders:

*  `ContentBlocks/ContentElements`
*  `ContentBlocks/PageTypes`
*  `ContentBlocks/RecordTypes`

The system loads them automatically as soon as it finds any folder inside these
directories, which has a file with the name **EditorInterface.yaml** inside.
Refer to the :ref:`YAML reference <yaml_reference>`, on how to define this file.

.. tip::

   Use the command :ref:`make:content-block <cb_skeleton>` to quickly create a
   new Content Block.

.. tip::

   You can copy and paste any Content Block from one project to another, and it
   will be automatically available.

Administration
==============

.. note::

   Make sure to add Content Blocks as a dependency in the host extension.

.. attention::

   You will need to allow the generated database fields, tables (if using inline
   relations) and CType in the backend user group permissions.
