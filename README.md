[![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-12.4-ff8700?maxAge=3600&logo=typo3)](https://get.typo3.org/)

# TYPO3 Content Blocks

This is the standalone repository for the TYPO3 Content Blocks project. Content
Blocks provides a new API to create custom Content Types like Content Elements,
Page Types or generic Record Types. Use it now in your TYPO3 v12 project and
eventually this will become a system extension in TYPO3 v13.

|                    | URL                                                                                 |
|--------------------|-------------------------------------------------------------------------------------|
| **Repository:**    | https://github.com/nhovratov/content-blocks                                         |
| **Documentation:** | https://docs.typo3.org/p/contentblocks/content-blocks/main/en-us/                   |
| **TER:**           | https://extensions.typo3.org/extension/content_blocks                               |
| **Packagist:**     | https://packagist.org/packages/contentblocks/content-blocks                         |
| **Examples:**      | https://github.com/nhovratov/content-blocks/tree/main/Build/content-blocks-examples |

## Roadmap

| Milestone                                                                             | Date          |
|---------------------------------------------------------------------------------------|---------------|
| [Content Blocks v0.6](https://github.com/nhovratov/content-blocks/releases/tag/0.6.0) | 26.02         |
| Content Blocks v0.7                                                                   | 01.04         |
| Core patch                                                                            | 01.04 - 23.04 |
| TYPO3 v13.1                                                                           | 23.04         |
| Review period                                                                         | 23.04 - 14.06 |
| Patch merged latest                                                                   | 01.07         |
| TYPO3 v13.2                                                                           | 02.07         |
| Content Blocks v1.0                                                                   | 02.07         |

## Installation

Require this package via composer:

```
composer req contentblocks/content-blocks
```

Or install it via the Extension Manager in the TYPO3 backend. The extension key
is `content_blocks`.

## Usage

Refer to the [Documentation](https://docs.typo3.org/c/contentblocks/content-blocks/main/en-us)
on how to use the Content Blocks API.

## Developing

There is a ddev setup ready to use. Ensure [ddev](https://github.com/ddev/ddev)
is installed on your machine. Then run:

```
ddev start
ddev composer install
touch .Build/public/FIRST_INSTALL
ddev launch
```

The default URL is https://content-blocks.ddev.site/.
Continue with the TYPO3 installation process.

## Testing

First install the composer dependencies:

```
composer install
```

Then run unit or functional tests by executing:

```
Build/Scripts/runTests.sh -s unit
Build/Scripts/runTests.sh -s functional
```

### Hint

Be sure to exclude the `.Build/public/typo3temp` directory from indexing in your IDE (e.g. PhpStorm) before starting the tests.

## Feedback

You can reach us on the TYPO3 Slack channel `#cig-structuredcontent`. We
appreciate any constructive feedback.

## FAQ

Q: Will the [content_blocks_reg_api](https://github.com/TYPO3-Initiatives/content-block-registration-api)
be further developed for TYPO3 v12?

A: The predecessor `content_blocks_reg_api`, which was initially created in 2020,
will not be further developed for TYPO3 v12. This system extension will replace
it completely. Migration steps will be provided as soon as we will reach a stable
development state.

This extension has been developed entirely from scratch and has, besides the
initial concept, nothing to do with the content_blocks_reg_api. The goal was to
create a system extension, which combines the best concepts from Content Blocks
and the [Mask](https://github.com/Gernott/mask) extension.
