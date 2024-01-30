.. include:: /Includes.rst.txt
.. _cb_nestedContentElements:

=======================
Nested Content Elements
=======================

It is possible to nest content elements within content blocks.
TYPO3 by default would render those nested elements within the TYPO3 backend
module "Page" and the frontend output.

EXT:content_blocks already delivers an API and integration for common setup to
prevent that unwanted output.

Concept
=======

The nesting is done via one database field holding a reference to the parent content element.
The :sql:`colPos` column will always be `0`.

.. note::

   This will not remove the need for proper grid extensions like EXT:container.

Preventing output in frontend
=============================

Output in frontend is prevented by extending :typoscript:`styles.content.get.where` condition.
This is done via the :typoscript:`postUserFunc` which will extract all necessary additional columns.
Those are than added to the SQL statement in order to prevent fetching of any child elements.

.. note::

   You need to integrate the logic yourself if you do not build upon :typoscript:`styles.content.get.where`.
   The necessary API providing all the columns is available via :php:`TYPO3\CMS\ContentBlocks\Service\TtContentParentField->getAllFieldNames()`.
   This can be used to apply the same approach to :php:`TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor`.

Preventing output in backend
============================

TYPO3 provides an event to alter the SQL query to fetch content for the "Page" backend module.
The EXT:content_blocks adds an event listener which will extract all necessary additional columns.
Those are than added to the SQL query in order to prevent fetching of any child elements.
