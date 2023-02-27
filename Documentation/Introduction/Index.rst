.. include:: /Includes.rst.txt
.. _introduction:

============
Introduction
============

   **Motivation:**

   Defining "Content Elements" in TYPO3 can be hard and the learning curve is
   steep. You need to learn PHP, TCA, TypoScript and Fluid and maybe other
   languages.

A Content Block is defined as a small chunk of information, which is connected
to a view and then rendered in the TYPO3 frontend.

The configuration of a Content Block is reduced and simplified via abstraction
and convention. A Content Block is configured as a reusable standalone composer
package, that needs all its dependencies to be defined.

What does it do?
================

Features:

*  API to register Content Blocks as composer packages
*  Generation of all configuration (TCA, TypoScript, TSconfig, database field
   definition) that is necessary for TYPO3
