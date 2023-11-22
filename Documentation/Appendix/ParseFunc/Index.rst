.. include:: /Includes.rst.txt

.. _cb_parsefunc:

=================
lib.parseFunc_RTE
=================

In order to process links inside RTE fields, one needs to define a so called
:ref:`parseFunc <t3tsref:parsefunc>` TypoScript snippet. This snippet is shipped
in the Core, when you use the `fluid_styled_content` system extension. If you
only rely on Content Blocks, you need to define it on your own.

There are multiple options. You can just simply copy the snippet from
`fluid_styled_content <https://github.com/TYPO3/typo3/blob/main/typo3/sysext/fluid_styled_content/Configuration/TypoScript/Helper/ParseFunc.typoscript>`__
and substitute the constants with your own values. Just remember to look for
changes after major TYPO3 releases. There might be new or deprecated options.

Another option could be to use a snippet from a popular ready-to-go sitepackages
like `bootstrap_package <https://github.com/benjaminkott/bootstrap_package/blob/v7.0.3/Configuration/TypoScript/Helper/ParseFunc.txt>`__.
However, these tend to be out of date so you need to check yourself, if it does
fit your (security) needs.
