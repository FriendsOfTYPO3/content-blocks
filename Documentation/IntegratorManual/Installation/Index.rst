.. include:: /Includes.rst.txt
.. _cb_installation:

==========================
Installing a Content Block
==========================

Installation
============

In composer mode, require your Content Block just like any other composer package.

.. code-block:: shell

    composer require <your-vendor>/<your-content-block>

For non-composer installations simply put your Content Block into the folder
`typo3conf/content-blocks/`.

Administration
==============

.. attention::
   You will need to allow the generated database fields, tables (if using inline
   relations) and CType in the backend user group permissions.

Security
========

.. attention::
   In non-composer-mode you'll need to modify your `.htaccess` or nginx
   configuration to secure the src folder of your Content Blocks.

.. tabs::

   .. group-tab:: .htaccess

        In .htaccess::

            # Add your own rules here.
            <If "%{REQUEST_URI} =~ m#^/typo3conf/content-blocks/.*\.(yaml|html|xlf|json)#">
                Order allow,deny
                Deny from all
                Satisfy All
            </If>

   .. group-tab:: .nginx

        In nginx::

            location ~ (?:typo3conf/contentBlocks)/[^/]+/(?:src?)/ {
                deny all;
            }
