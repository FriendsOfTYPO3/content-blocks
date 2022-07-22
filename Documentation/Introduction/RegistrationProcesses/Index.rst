.. include:: /Includes.rst.txt
.. _registration_processes:

================================
Registration processes explained
================================

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

Validating a Content Block
--------------------------

.. note::
   **Not yet implemented!**
   See `validation of package files <https://github.com/TYPO3-Initiatives/content-block-registration-api/issues/7>`__
   and `validation of the editing interface <https://github.com/TYPO3-Initiatives/content-block-registration-api/issues/8>`_.
   Basically a YAML schema validation (based on JSON schema) is needed here.
   Exchange with the Form Framework team is targeted.

If a Content Block is invalid, it won’t be available in the TYPO3 backend for
editors. An error message is available in the “Check for broken Content Blocks”
tool in the maintenance area. Additional information to composer could be added
via a composer plugin to validate the definition during installation.


Mapping to the database
-----------------------

There are :ref:`several variants<data_storage_variants>` of how data of a content
block can be stored and retrieved from the database. Currently, there is no
decision on the desired storage method, because performance research is still
in progress.

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
