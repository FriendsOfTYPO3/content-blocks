.. include:: /Includes.rst.txt
.. _field_type_basic:

========
Basic
========

Basics are a concept like partials or templates in fluid. They are used to
have a predefined set of fields that can be used and has to defined only once.

There are basically two ways of using it.

The first way is to use Basics on level 0 in EditorInterface.yaml. This way the Basics
are added after the fields of the content block. This is useful if you want to have
a set of fields that are always available for a content block.

This is eg. how to add the classic Fluid Styled Content Tabs like Appearance, Access or Language.
These Basics are shiplped with the content blocks out of the box.

List of the standard Basics shipped with the content blocks:

* Typo3StandardAppearance
* Typo3StandardAccess
* Typo3StandardLanguage
* Typo3StandardGeneral

`Typo3StandardGeneral` is always added to tt_content before the content block fields.

How to use it on level 0:

.. code-block:: yaml

    name: example/basics
    group: common
    basics:
        - Typo3StandardAccess
        - Typo3StandardLanguage
    fields:
        # - ...

The second way is to use Basics in fields. This can be easily done by using the identifier
and the type `Basic`.


.. code-block:: yaml

    name: example/basics
    group: common
    basics:
        - Typo3StandardAccess
        - Typo3StandardLanguage
    fields:
        - identifier: header
          useExistingField: true
        - identifier: Typo3StandardAppearance
          type: Basic


You can define your own Basics by dump a ContentBlocksBasic.yaml to your e.g. sitepackage in
Configuration/Yaml/ContentBlocksBasic.yaml. If you want to create more then one Basic, we recommend
to split them up in seperate files and include them in ContentBlocksBasic.yaml.

Example how to create a singel Basic:

.. code-block:: yaml

    Basics:
        - identifier: Typo3StandardAccess
            fields:
            - identifier: access_tab
                type: Tab
            # - ...

You can go on with normal content block field definitions.

Example how to include multiple separated Basics in diffrent sub files:

.. code-block:: yaml

    imports:
        - { resource: "./Basics/Typo3StandardGeneral.yaml" }
        - { resource: "./Basics/Typo3StandardAppearance.yaml" }
        - { resource: "./Basics/Typo3StandardLanguage.yaml" }
        - { resource: "./Basics/Typo3StandardAccess.yaml" }


This examples can also be found in the content blocks extension in the Configuration/Yaml/ directory.
    