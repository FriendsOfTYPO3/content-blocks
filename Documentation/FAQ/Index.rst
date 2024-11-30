.. include:: /Includes.rst.txt
.. _faq:

===
FAQ
===

Answers to frequently asked questions.

..  accordion::
    :name: faq

    ..  accordion-item:: Which parts of Content Blocks are in TYPO3 v13 Core?
        :name: integration-v13
        :header-level: 2
        :show:

        * `Feature: #104002 - Schema API <https://docs.typo3.org/permalink/changelog:feature-104002-1718273913>`_
        * `Feature: #103581 - Automatically transform TCA field values for record objects <https://docs.typo3.org/permalink/changelog:feature-103581-1723209131>`_
        * `Feature: #103783 - RecordTransformation Data Processor <https://docs.typo3.org/permalink/changelog:feature-103783-1715113274>`_

        Content Blocks is partly integrated into TYPO3 v13. Since version 1.0 it
        is based on the new, underlying API from the Core. Most importantly the
        new Record Transformation API, which was previously implemented in
        Content Blocks for v12.

    ..  accordion-item:: Why YAML and not PHP?
        :name: yaml
        :header-level: 2

        Content Blocks is supposed to be the intersection where frontend and
        backend developers meet. YAML is currently the best suited format for
        both parties. It allows comments and auto-completion for IDEs with JSON
        schemas.

    ..  accordion-item:: How can I extend an existing Content Block?
        :name: extending-types
        :header-level: 2

        This is not possible. The philosophy behind Content Blocks are reusable
        components, not extendable components. If there is an existing Content
        Block you want to adapt to your custom needs, simply copy it as a whole.
        There is no drawback to it. Normally, you don't even want future changes
        to a Content Block from external parties. By owning the Content Block
        your code can't break with an update.
