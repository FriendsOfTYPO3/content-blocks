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
