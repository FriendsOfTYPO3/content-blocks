[![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-12.4-ff8700?maxAge=3600&logo=typo3)](https://get.typo3.org/)
[![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-13.4-ff8700?maxAge=3600&logo=typo3)](https://get.typo3.org/)

# TYPO3 CMS Content Blocks

This is the standalone repository for the TYPO3 Content Blocks project. Content
Blocks provides a new API to create custom Content Types like Content Elements,
Page Types or generic Record Types. Use it now in your TYPO3 v12/v13 project and
eventually this will become a Core feature in TYPO3 v14 LTS.

|                    | URL                                                                                      |
|--------------------|------------------------------------------------------------------------------------------|
| **Repository:**    | https://github.com/friendsoftypo3/content-blocks                                         |
| **Documentation:** | https://docs.typo3.org/p/friendsoftypo3/content-blocks/main/en-us/                       |
| **TER:**           | https://extensions.typo3.org/extension/content_blocks                                    |
| **Packagist:**     | https://packagist.org/packages/friendsoftypo3/content-blocks                             |
| **Examples:**      | https://github.com/friendsoftypo3/content-blocks/tree/main/Build/content-blocks-examples |

## Installation

Require this package via composer:

```
composer req friendsoftypo3/content-blocks
```

Or install it via the Extension Manager in the TYPO3 backend. The extension key
is `content_blocks`.

## Usage

Refer to the [Documentation](https://docs.typo3.org/p/friendsoftypo3/content-blocks/main/en-us)
on how to use the Content Blocks API.

##  Feedback and Support

You can reach us on the [TYPO3 Slack](https://typo3.org/community/meet/chat-slack)
channel `#cig-structuredcontent`. We  appreciate any constructive feedback and
will help you, if you have any problems.

## JSON Schema

Enable validation and auto-completion with [JSON schema](https://github.com/nhovratov/content-blocks-json-schema)

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

## Rendering the documentation

When you update the documentation you can try out rendering it locally
(Docker required):

```
make docs
```

You can test if the syntax and references are ok with

```
make test-docs
```

## Roadmap 2024

| Milestone                                                                                               | Date          |
|---------------------------------------------------------------------------------------------------------|---------------|
| [Content Blocks v0.6](https://github.com/friendsoftypo3/content-blocks/releases/tag/0.6.0)              | 26.02         |
| [Content Blocks v0.7](https://github.com/friendsoftypo3/content-blocks/releases/tag/0.7.0)              | 01.04         |
| [Core patch](https://review.typo3.org/c/Packages/TYPO3.CMS/+/83721)                                     | 01.04 - 23.04 |
| [TYPO3 v13.1](https://typo3.org/article/typo3-v131-the-surfers-starterkit)                              | 23.04         |
| [Review period](https://review.typo3.org/c/Packages/TYPO3.CMS/+/83721/19)                               | 23.04 - 14.06 |
| [TYPO3 v13.2](https://typo3.org/article/typo3-v132-ready-set-ride)                                      | 02.07         |
| [Content Blocks compatibility v13](https://github.com/friendsoftypo3/content-blocks/releases/tag/0.8.0) | 11.07         |
| [Content Blocks (partly) Core integration](https://typo3.org/article/typo3-v133-prepare-for-the-fun)    | 17.09         |
| [Content Blocks 1.0](https://github.com/friendsoftypo3/content-blocks/releases/tag/1.0.0)               | 15.10         |
