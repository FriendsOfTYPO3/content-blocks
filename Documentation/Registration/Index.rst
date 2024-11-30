.. include:: /Includes.rst.txt
.. _cb_installation:

============
Registration
============

In order to register a new Content Block, a folder **ContentBlocks** has to be
created on the root level inside a loaded extension. Depending on the Content
Type you want to create, you place the new Content Block into a dedicated
folder. These are named **ContentElements**, **PageTypes** and **RecordTypes**.

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 4

            *   :path:`my_extension`

                *   :path:`Classes`

                *   :path:`Configuration`

                *   :path:`ContentBlocks`

                    *   :path:`ContentElements`

                        *   :path:`content-block-1`
                        *   :path:`content-block-2`

                    *   :path:`PageTypes`

                        *   :path:`content-block-3`
                        *   :path:`content-block-4`

                    *   :path:`RecordTypes`

                        *   :path:`content-block-5`
                        *   :path:`content-block-6`

                *   :file:`ext_emconf.php`
                *   :file:`composer.json`

The system loads them automatically as soon as it finds any folder inside these
directories, which has a file with the name :ref:`config.yaml <cb_definition_editor_interface>`
inside. Refer to the :ref:`YAML reference <yaml_reference>`, on how to define
this file.

.. tip::

   Use the command :ref:`make:content-block <cb_skeleton>` to quickly create a
   new Content Block.

.. tip::

   You can copy and paste any Content Block from one project to another, and it
   will be automatically available.

Administration
==============

Make sure to add Content Blocks as a dependency in the host extension.

You will need to allow the generated database fields, tables
(if using :ref:`Collections <field_type_collection>`) and CType in the backend
user group permissions.
