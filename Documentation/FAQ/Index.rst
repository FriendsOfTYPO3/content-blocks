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

        If you really need to override a Content Block, you can always fall back
        to traditional methods like :ref:`TCA Overrides <cb_extendTca>`.

    ..  accordion-item:: How can I extend an existing Content Block?
        :name: yaml
        :header-level: 2

        Same as before: This is not possible by default. Nevertheless, you can
        always extend the TypoScript the old way and use e.g. partials from your
        sitepackage or libraries from outside. But this way you are on your own.

    ..  accordion-item:: Can I add PHP code to my content blocks?
        :name: add-php-code
        :header-level: 2

        The Content Blocks API does support PHP files at all. If you want to add
        some PHP logic to your content blocks, you have to use the old common
        ways.

    ..  accordion-item:: Can I use twig engine with Content Blocks?
        :name: twig-in-content-blocks
        :header-level: 2

        Not out of the box. As the main rendering engine for TYPO3 is fluid, you
        have to do some custom work to get twig working with Content Blocks.

    ..  accordion-item:: Can I use headless with Content Blocks?
        :name: headless-with-content-blocks
        :header-level: 2

        Content Blocks generating the TYPO3 core things under the hood (TCA,
        TypoScript, tsConfig), so you have to prepare your headless setup like
        you have to do with the core content elements.

    ..  accordion-item:: Are the Content Blocks assets (JS and CSS) compressed by the the core?
        :name: assets-compression
        :header-level: 2

        Content Blocks using the core asset collector, so the assets not get
        merged together. Since they get registered only if the content block is
        used on the page, each file is included separate.

    ..  accordion-item:: Can I add Content Blocks assets (JS and CSS) to my build process?
        :name: assets-build-process
        :header-level: 2

        This is not a specific Content Blocks question. You can add your assets
        to the build process like you do with any other assets. E.G. you can add
        your Content Block CSS to your main CSS file, and remove the CSS
        registration in the Content Block.

    ..  accordion-item:: Can I add Content Blocks assets (JS and CSS) to my build process?
        :name: assets-build-process
        :header-level: 2

        This is not a specific Content Blocks question. You can add your assets
        to the build process like you do with any other assets. E.G. you can add
        your Content Block CSS to your main CSS file, and remove the CSS
        registration in the Content Block.

    ..  accordion-item:: Will there be a GUI (Graphical User Interface) for
        Content Blocks?
        :name: content-blocks-gui
        :header-level: 2

        Yes, the Content Types Team is working on a GUI for Content Blocks. The
        first steps are already done, and we are looking forward to the first
        beta phase in 2025.
