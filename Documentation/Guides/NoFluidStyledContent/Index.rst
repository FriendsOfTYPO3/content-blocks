.. include:: /Includes.rst.txt

.. _no-fsc-guide:

=========================================
Installation without Fluid Styled Content
=========================================

It is (and was) always possible to run a TYPO3 installation without the optional
extension `fluid_styled_content` (fsc). With Content Blocks this is even easier
as you can create any Content Element you need really fast. If you need a
specific Content Element definition from fsc, you can simply re-create it.
However, there are some odd specialties which you will encounter when omitting
fsc.

Plugins and contentRenderingTemplates
=====================================

Plugins from other extensions probably won't work without `fluid_styled_content`
as the rendering definition :typoscript:`lib.contentElement` is defined there
for both `CType`-based and `list_type`-based plugins. So you have to define it
yourself e.g. in your sitepackage.

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/TypoScript/setup.typoscript

    # Basic rendering of list-type plugins. Normally defined in fluid_styled_content.
    tt_content.list = FLUIDTEMPLATE
    tt_content.list {
      template = TEXT
      template.value = <f:cObject typoscriptObjectPath="tt_content.list.20.{data.list_type}" table="tt_content" data="{data}"/>
    }

    # Specific rendering for CType plugins. Normally defined in fluid_styled_content.
    # This needs to be added for every registered plugin separately.
    tt_content.plugin_name = FLUIDTEMPLATE
    tt_content.plugin_name {
      template = TEXT
      template.value = <f:cObject typoscriptObjectPath="tt_content.{data.CType}.20" data="{data}" table="tt_content" />
    }

The first snippet above defines the default rendering definition for the
record type `list`. This is a generic type for plugins, which further specify
their type in the field `list_type`.

The second snippet defines a rendering definition for a specific CType-based
plugin. As these plugins are independent from each other, this needs to be added
for every plugin. Tip: If you don't want to repeat this step over and over again
it is also possible to define your own :typoscript:`lib.contentElement` with
the :typoscript:`FLUIDTEMPLATE` cObject:

.. code-block:: typoscript

    lib.contentElement = FLUIDTEMPLATE
    lib.contentElement {
        templateRootPaths {
            0 = EXT:sitepackage/Resources/Private/Templates/
        }
    }

Then, add a template with name `Generic.html`. Add the cObject ViewHelper from
the snippet above and it will work automatically. This can also be done for
list-type plugin with `List.html`.

Which method you choose is up to you. The pure TypoScript variant is more
robust as the template one, as changes are to be expected in the Core in this
area.

contentRenderingTemplates
-------------------------

One thing is still missing: The so called `contentRenderingTemplates`
definition. As the name suggests, it defines where the templates for the content
rendering are defined. Without fsc you probably don't have one. And without this
definition the TypoScript from above won't work, as it will be loaded too late
in the TypoScript parsing process.

.. code-block:: php
   :caption: EXT:site_package/ext_localconf.php

    // Define TypoScript as content rendering template.
    // This is normally set in Fluid Styled Content.
    $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'sitepackage/Configuration/TypoScript/';
