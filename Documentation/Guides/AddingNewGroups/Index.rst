.. include:: /Includes.rst.txt
.. _cb_guide_new_groups:

=================
Adding new groups
=================

There are two types of groups. One is for the NewContentElementWizard. This is
where you can select Content Elements from a module with tab navigation. Each
tab item is a group. This is the group, which you define in the YAML config.

The second type is the one in the type selector box. Also called record type
selector. The select items are grouped there with TCA :php:`itemGroups`. The
v12 version of Content Blocks hard-codes this group to "Content Blocks".

.. note::

   In TYPO3 v13 these two groups are unified. Defining the second type will
   automatically register the first type.

In TYPO3 v12
============

In v12 you have to add a group with page TSconfig.

.. code-block:: typoscript
   :caption: EXT:my_package/Configuration/page.tsconfig

    mod.wizards.newContentElement.wizardItems {
        my_group {
            header = LLL:EXT:my_package/Resources/Private/Language/Backend.xlf:content_group.my_group
            before: common
        }
    }

Now you can assign the new group :typoscript:`my_group` to the YAML
:yaml:`group` option.

In TYPO3 v13
============

In v13 you need to register the group via PHP API. This will add a new group
both for the NewContentElementWizard and the type selector box.

.. code-block:: php
   :caption: EXT:my_package/Configuration/TCA/Overrides/tt_content.php

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
        'tt_content',
        'CType',
        'my_group',
        'My group label or LLL:EXT reference',
        'before:default',
    );
