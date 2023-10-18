.. include:: /Includes.rst.txt
.. _cb_installation:

==========================
Installing a Content Block
==========================

Installation
============

Content Blocks can be placed inside the `ContentBlocks/ContentElements`,
`ContentBlocks/PageTypes` or `ContentBlocks/RecordTypes` folder at
any of your extension. The system determines the type and loads them
automatically. Make sure to add Content Blocks as a dependency in the host
extension. This will ensure correct loading order an enables to override
Content Blocks TCA.

Administration
==============

.. attention::

   You will need to allow the generated database fields, tables (if using inline
   relations) and CType in the backend user group permissions.
