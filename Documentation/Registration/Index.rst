.. include:: /Includes.rst.txt
.. _cb_installation:

========================
Register a Content Block
========================

Registration
============

Content Blocks can be placed inside the `ContentBlocks/ContentElements`,
`ContentBlocks/PageTypes` or `ContentBlocks/RecordTypes` folder at
any of your extensions. The system determines the type and loads them
automatically. Make sure to add Content Blocks as a dependency in the host
extension.

Administration
==============

.. attention::

   You will need to allow the generated database fields, tables (if using inline
   relations) and CType in the backend user group permissions.
