.. include:: /Includes.rst.txt
.. _cb_definition:

==========
Definition
==========

The minimal viable definition consists of a folder with a YAML file named
**config.yaml** inside. All other resources are split into three folders
named **assets**, **templates** and **language**.

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 4

            *   :path:`my-content-block`

                *   :path:`assets`(:ref:`details <cb_definition_assets>`)

                    *   :file:`icon.svg`

                *   :path:`language`(:ref:`details <cb_definition_language>`)

                    *   :file:`labels.xlf`

                *   :path:`templates`(:ref:`details <cb_definition_source>`)

                    *   :path:`partials`

                        *   :file:`MyPartial.html`

                    *   :file:`backend-preview.fluid.html`
                    *   :file:`frontend.fluid.html`

                *   :file:`config.yaml`(:ref:`details <cb_definition_editor_interface>`)

..  toctree::
    :titlesonly:

    ConfigYaml/Index
    Assets/Index
    Templates/Index
    Language/Index
