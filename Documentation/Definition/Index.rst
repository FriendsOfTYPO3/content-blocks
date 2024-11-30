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

                *   :path:`assets`

                    *   :file:`icon.svg`

                *   :path:`language`

                    *   :file:`labels.xlf`

                *   :path:`templates`

                    *   :path:`partials`

                        *   :file:`MyPartial.html`

                    *   :file:`backend-preview.html`
                    *   :file:`frontend.html`

                *   :file:`config.yaml`

..  toctree::
    :titlesonly:

    ConfigYaml/Index
    Assets/Index
    Templates/Index
    Language/Index
