.. include:: /Includes.rst.txt
.. _elements-ordering:

=================
Elements Ordering
=================

By default, there is no deterministic ordering for Content Blocks. The main
way to have an order in place is to use the priority system. Each Content Block
can have an individual priority, which is simply a number. The higher, the more
priority it has and will be displayed before elements with less or no priority.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/my-element/config.yaml

    name: example/my-element
    priority: 20


Dependency ordering
===================

.. versionadded:: TYPO3 v14.2

An alternative to the priority system is a dependency ordering, which is part
of the TYPO3 Core and based on pageTS config. There you can define, whether
an element should be displayed before or after another element.

Syntax:

:typoscript:`mod.wizards.newContentElement.wizardItems.{group}.elements.{typeName}.[before|after] = {typeName,[...]}`

..  code-block:: typoscript
    :caption: EXT:your_extension/ContentBlocks/ContentElements/my-element/page.tsconfig

    mod.wizards.newContentElement.wizardItems {
        default.elements {
            textmedia {
                after = header
            }
            article_card {
                after = textmedia
            }
            # Multiple elements can be specified (comma-separated)
            article_list {
                after = header,textmedia
            }
            # Or use before
            header {
                before = textmedia
            }
        }
    }

.. tip::

    Since Content Blocks version 2.3 you can place a page.tsconfig file inside
    your Content Block folder and use it as a :ref:`Site Set <api_site_sets>`.


.. tip::

    This feature is especially helpful, if you want to position your Content
    Blocks relative to a core-defined element.
