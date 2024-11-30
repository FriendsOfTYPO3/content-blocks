.. include:: /Includes.rst.txt
.. _api_content_elements:

================
Content Elements
================

For YAML reference refer to :ref:`this page <yaml_reference_content_element>`.

Content Elements are a special Content Type in TYPO3. The basic structure is
already defined in the TYPO3 Core. Content Blocks only adds new types to it.

A minimal Content Element looks like this:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/cta/config.yaml

    name: example/cta
    fields:
      - identifier: header
        useExistingField: true

In case you need the well-known `Appearance` tab back, you can add pre-defined
Basics to your definition:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/cta/config.yaml

    name: example/cta
    basics:
        - TYPO3/Appearance
        - TYPO3/Links
    fields:
      - identifier: header
        useExistingField: true

The Appearance tab will then be added after all your custom fields.

.. _about_content_elements:

About TYPO3 Content Elements
============================

   Defining "Content Elements" in TYPO3 is hard and the learning curve is steep.

Despite the possibility to customize TYPO3 to ones needs, most people used the
standard Content Elements shipped with TYPO3 Core. This is, of course,
convenient, but has several drawbacks as soon as customizations are needed. One
override follows the next, until the Core Content Element has more modifications
than the initial implementation. This is where the concept of Content Blocks
stepped in. First of all, it makes creating new types a no-brainer, so that the
temptation to overrides is weakened. Secondly, for the same reason, it makes
standard Content Elements almost obsolete. If you need a specific element, you
can always copy/paste it into your project as your **own** element. Owning the
elements means there will be no breaking changes to the Fluid templates.

Related documentation:

*   :ref:`Templating <cb_templating>`
*   :ref:`Nested Content Elements <cb_nestedContentElements>`
*   :ref:`Fluid Styled Content Layouts <fsc-guide>`
*   :ref:`Installation without Fluid Styled Content <no-fsc-guide>`
*   :ref:`Extend TypoScript <cb_extendTyposcript>`
