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

.. _cb_parsefunc:

lib.parseFunc_RTE
=================

In order to process links inside RTE fields, one needs to define a so called
:ref:`parseFunc <t3tsref:parsefunc>` TypoScript snippet. This snippet is shipped
in the Core, when you use the `fluid_styled_content` system extension. If you
only rely on Content Blocks, you need to define it yourself.

There are multiple options. You can just simply copy the snippet from
`fluid_styled_content <https://github.com/TYPO3/typo3/blob/main/typo3/sysext/fluid_styled_content/Configuration/TypoScript/Helper/ParseFunc.typoscript>`__
and substitute the constants with your own values. Just remember to look for
changes after major TYPO3 releases. There might be new or deprecated options.

Another option could be to use a snippet from popular ready-to-go sitepackages
like `bootstrap_package <https://github.com/benjaminkott/bootstrap_package/blob/v7.0.3/Configuration/TypoScript/Helper/ParseFunc.txt>`__.
However, these tend to be out of date so you need to check yourself, if it does
fit your (security) needs.

Plugins and contentRenderingTemplates
=====================================

Plugins from other extensions probably won't work without `fluid_styled_content`
as the rendering definition is defined there for both `CType`-based and
`list_type`-based plugins. So you have to define it yourself e.g. in your
sitepackage.

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/TypoScript/setup.typoscript

    # Basic rendering of list-type plugins. Normally defined in fluid_styled_content.
    tt_content.list = FLUIDTEMPLATE
    tt_content.list {
      template = TEXT
      template.value = <f:cObject typoscriptObjectPath="tt_content.list.20.{data.list_type}" table="tt_content" data="{data}"/>
    }

    # @todo snippet for CType based plugins.

The TypoScript snippet above defines the default rendering definition for the
record type `list`. This is a generic type for plugins, which further specify
their type in the field `list_type`.

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
