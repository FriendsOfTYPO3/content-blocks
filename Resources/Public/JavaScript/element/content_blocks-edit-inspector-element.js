/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
var __decorate=function(e,t,o,n){var r,c=arguments.length,l=c<3?t:null===n?n=Object.getOwnPropertyDescriptor(t,o):n;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)l=Reflect.decorate(e,t,o,n);else for(var i=e.length-1;i>=0;i--)(r=e[i])&&(l=(c<3?r(l):c>3?r(t,o,l):r(t,o))||l);return c>3&&l&&Object.defineProperty(t,o,l),l};import{html,LitElement}from"lit";import{customElement,property}from"lit/decorators.js";import{MainController}from"@typo3/content-blocks/controller/main-controller.js";import"@typo3/backend/element/spinner-element.js";import"@typo3/backend/element/icon-element.js";let ContentBlocksEditInspectorElement=class extends LitElement{constructor(){super(...arguments),this._controller=MainController.instance(this)}createRenderRoot(){return this}connectedCallback(){super.connectedCallback()}render(){const e=this._controller.currentContentBlock.fields[this.fieldIdentifier]??null;return e?html`
      <h2>${e.identifier}</h2>
      ${e.type}
      <code>${JSON.stringify(e.properties)}</code>
    `:html`
        <typo3-content_blocks-choose-name
        ></typo3-content_blocks-choose-name>
      `}};__decorate([property()],ContentBlocksEditInspectorElement.prototype,"cType",void 0),__decorate([property({attribute:"field-identifier"})],ContentBlocksEditInspectorElement.prototype,"fieldIdentifier",void 0),ContentBlocksEditInspectorElement=__decorate([customElement("typo3-content_blocks-edit-inspector")],ContentBlocksEditInspectorElement);export{ContentBlocksEditInspectorElement};