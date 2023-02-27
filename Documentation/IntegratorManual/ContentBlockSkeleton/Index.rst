.. include:: /Includes.rst.txt
.. _cb_skeleton:

==========================
Create a Content Block skeleton
==========================


Create a Content Block skeleton	on your terminal
====================

This command creates a Content Block skeleton for you.
It creates the basic structure of a Content Block, by vendor, package, and
on composer mode with the path to your packages folder.
If no path is given, it will be created in {publicDir}/typo3conf/content-blocks directory.

Example using ddev and creating a Content Block skeleton in one line:

.. code-block:: bash

    ddev typo3 make:content-block --vendor=foo --package=bar --path=packages/content-blocks


If you do not want to use the options, you can also use the interactive mode:
(ddev example)

.. code-block:: bash

    ddev typo3 make:content-block


This will ask you for the following information:
(the question to the path will only be asked on composer mode)

    Enter your vendor name:
    Enter your package name:
    Enter your relative path (Default is {publicDir}/typo3conf/content-blocks):


If you do not use ddev, the command will be like this:
(depending on your setup where the bin directory is)

.. code-block:: bash

    ./bin/typo3 make:content-block
