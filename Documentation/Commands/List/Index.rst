.. include:: /Includes.rst.txt
.. _command_list:

============
List command
============

The command :bash:`content-blocks:list` lists all loaded Content Blocks inside
a table.

Options
=======

.. confval:: order

   :Required: false
   :Type: string
   :Shortcut: o

   :bash:`vendor`, :bash:`name`, :bash:`table`, :bash:`type-name`, :bash:`content-type` or :bash:`extension`

This will give you an overview of all available Content Blocks:

.. code-block:: bash

   vendor/bin/typo3 content-blocks:list

Alternatively, you can specify a different order in which to sort the result:

.. code-block:: bash

   vendor/bin/typo3 content-blocks:list --order content-type

Example output:

.. code-block:: bash

    +-----------+-------------------+-------------------------+-----------------+-----------------------------+
    | vendor    | name              | extension               | content-type    | table                       |
    +-----------+-------------------+-------------------------+-----------------+-----------------------------+
    | example   | tabs              | content_blocks_examples | Content Element | tt_content                  |
    | example   | accordion         | content_blocks_examples | Content Element | tt_content                  |
    | example   | card-group        | content_blocks_examples | Content Element | tt_content                  |
    | example   | cta               | content_blocks_examples | Content Element | tt_content                  |
    | example   | icon-group        | content_blocks_examples | Content Element | tt_content                  |
    | example   | imageslider       | content_blocks_examples | Content Element | tt_content                  |
    | example   | example-page-type | content_blocks_examples | Page Type       | pages                       |
    | hov       | record1           | content_blocks_examples | Record Type     | tx_hov_domain_model_record1 |
    | hov       | record2           | content_blocks_examples | Record Type     | tx_hov_domain_model_record1 |
    | hov       | notype            | content_blocks_examples | Record Type     | tx_hov_domain_model_notype  |
    +-----------+-------------------+-------------------------+-----------------+-----------------------------+
