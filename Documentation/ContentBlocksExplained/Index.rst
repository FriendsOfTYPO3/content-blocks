.. include:: /Includes.rst.txt
.. _cb_explained:

=====================================
Content Blocks Registration Explained
=====================================

Abstraction Requirements
========================

To achieve the goal of reducing the complexity of Content Block registration
the `facade pattern <https://en.wikipedia.org/wiki/Facade_pattern>`__
approach needs to be used for some of TYPO3s internal APIs. These are

*  Validation
*  Mapping to the database
*  TCA generation for

   *  ext_tables.php
   *  Configuration/TCA/….
   *  registration of the icon in the CType field in TCA

*  Registration of the plugin to display the content for frontend rendering
   including DataProcessors
*  Registration of the icon in the new content element wizard (PageTS)
*  Configuration of the template path(s)
*  Registration for the preview in the backend


Processes in detail
===================

Detecting a Content Block
-------------------------

In composer installations a Content Block needs to be of type `typo3-cms-contentblock`.
For non-composer installations the folder `typo3conf/contentBlocks/` is checked
to detect Content Blocks.

Mapping to the database
-----------------------

The fields defined in the `EditorInterface.yaml` are created in the data base by
extending the `tt_content` table. Strong defaults are used to map the TCA field
types to the database column types.

Virtual generation of TCA (ext_tables.php)
------------------------------------------

Requirements:

*  Has to be after non override TCA loading
*  Has to be before the caching of the TCA
*  Has to be before merging the overrides for TCA

TCA is virtually generated from the class implementing a Content Block field type.

Generate registration of the plugin
-----------------------------------

Requirements:

*  Register icon
*  Add TCA entry in CTypes list including the icon
*  Register plugin in TYPO3
*  Add TypoScript to render the content plugin
*  Add PageTS for the Content Block

   *  Define where to display (group / location) the Content Block in the new
      content element wizard
