.. include:: /Includes.rst.txt
.. _field_type_basic:

======
Basics
======

Basics are a concept like partials or mixins. They are used to have a
pre-defined set of fields that can be reused and have to defined only once.

There are two different ways of using it.

The first way is to use Basics like layouts for your UI. The Basics are added
after the fields of your content block. This is useful if you want to have
a set of fields that are always available for a content block.

List of the standard Basics shipped with Content Blocks:

*  Typo3StandardAppearance
*  Typo3StandardLinks
*  Typo3StandardCategories

This is an example on how to add the classic Fluid Styled Content Tab
"Appearance":

.. code-block:: yaml

    name: example/basics
    group: common
    basics:
        - Typo3StandardAppearance
    fields:
        # - ...

The second way is to use Basics directly between your custom fields. This can
be done by using the identifier and the type :yaml:`Basic`.

.. code-block:: yaml

    name: example/basics
    group: common
    basics:
        - Typo3StandardAppearance
    fields:
        - identifier: header
          useExistingField: true
        - identifier: Typo3StandardLinks
          type: Basic

You can define your own Basics by placing a
`Configuration/Yaml/ContentBlocksBasic.yaml` file into your sitepackage. If you
want to create more then one Basic, it is recommended to split them up in
separate files and include them in ContentBlocksBasic.yaml.

Example on how to create a single Basic:

.. code-block:: yaml

    Basics:
        - identifier: YourCustomBasic
            fields:
            - identifier: your_tab
                type: Tab
            # - ...

You can continue with normal content block field definitions.

Example on how to include multiple separated Basics in different sub files:

.. code-block:: yaml

    imports:
        - { resource: "./Basics/Typo3StandardAppearance.yaml" }
        - { resource: "./Basics/Typo3StandardLinks.yaml" }
        - { resource: "./Basics/Typo3StandardCategories.yaml" }


This examples can also be found in the content blocks extension in th
`Configuration/Yaml` directory.
