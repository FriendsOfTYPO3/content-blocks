.. include:: /Includes.rst.txt
.. _cb_guide_new_groups:

=================
Adding new groups
=================

You need to register the group via PHP API. This will add a new group both for
the "NewContentElementWizard" and the record type selector box. It works the
same for Page Types and Record Types. Adjust the table and typeField
accordingly.

.. code-block:: php
   :caption: EXT:my_package/Configuration/TCA/Overrides/tt_content.php

    <?php

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
        'tt_content', // table
        'CType', // typeField
        'my_group', // group
        'My group label or LLL:EXT reference', // label
        'before:default', // position
    );
