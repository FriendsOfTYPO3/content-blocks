.. include:: /Includes.rst.txt
.. _cb_definition:

==========
Definition
==========

The minimal viable definition consists of a folder with a YAML file named
**EditorInterface.yaml** inside. All other resources are split into two folders
named **Assets** and **Source**. These include public resources, translations
and templates.

.. code-block:: none
   :caption: Directory structure of a Content Block

   ├── Assets
   │   └── Icon.svg
   ├── Source
   │   ├── Language
   │   │   └── Labels.xlf
   │   ├── Partials
   │   │   └── Component.html
   │   ├── EditorPreview.html
   │   └── Frontend.html
   └── EditorInterface.yaml

..  toctree::
    :titlesonly:

    EditorInterface/Index
    Assets/Index
    Source/Index
    Language/Index
