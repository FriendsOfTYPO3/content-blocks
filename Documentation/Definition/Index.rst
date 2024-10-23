.. include:: /Includes.rst.txt
.. _cb_definition:

==========
Definition
==========

The minimal viable definition consists of a folder with a YAML file named
**config.yaml** inside. All other resources are split into three folders
named **assets**, **templates** and **language**.

.. code-block:: none
   :caption: Directory structure of a Content Block

   ├── assets
   │   └── icon.svg
   ├── language
   │    └── labels.xlf
   ├── templates
   │   ├── partials
   │   │   └── Component.html
   │   ├── backend-preview.html
   │   └── frontend.html
   └── config.yaml

..  toctree::
    :titlesonly:

    ConfigYaml/Index
    Assets/Index
    Templates/Index
    Language/Index
