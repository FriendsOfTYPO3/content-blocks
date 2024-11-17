.. include:: /Includes.rst.txt
.. _extension_installation:

============
Installation
============

Composer installations
======================

.. code-block:: shell

    composer require friendsoftypo3/content-blocks

Classic mode
============

Activate the Content Blocks extension in the Extension Manager.

Security
--------

In classic mode it is important to deny access to the Content Blocks folder
by the webserver. For this a small adjustment to the standard TYPO3 .htaccess
file in the section "Access block for folders" is needed:

.. tabs::

   .. group-tab:: apache

       ..  code-block:: diff
           :caption: .htaccess.diff

           -RewriteRule (?:typo3conf/ext|typo3/sysext|typo3/ext)/[^/]+/(?:Configuration|Resources/Private|Tests?|Documentation|docs?)/ - [F]
           +RewriteRule (?:typo3conf/ext|typo3/sysext|typo3/ext)/[^/]+/(?:Configuration|ContentBlocks|Resources/Private|Tests?|Documentation|docs?)/ - [F]


   .. group-tab:: nginx

       ..  code-block:: diff
           :caption: nginx configuration

           -location ~ (?:typo3conf/ext|typo3/sysext|typo3/ext)/[^/]+/(?:Configuration|Resources/Private|Tests?|Documentation|docs?)/ {
           +location ~ (?:typo3conf/ext|typo3/sysext|typo3/ext)/[^/]+/(?:Configuration|ContentBlocks|Resources/Private|Tests?|Documentation|docs?)/ {
                deny all;
            }
