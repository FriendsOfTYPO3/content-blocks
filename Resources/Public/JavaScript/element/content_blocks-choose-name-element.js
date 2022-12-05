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
var __decorate=function(e,t,o,n){var a,i=arguments.length,c=i<3?t:null===n?n=Object.getOwnPropertyDescriptor(t,o):n;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)c=Reflect.decorate(e,t,o,n);else for(var r=e.length-1;r>=0;r--)(a=e[r])&&(c=(i<3?a(c):i>3?a(t,o,c):a(t,o))||c);return i>3&&c&&Object.defineProperty(t,o,c),c};import{html,LitElement}from"lit";import{customElement,property}from"lit/decorators.js";import{createRef,ref}from"lit/directives/ref.js";import"@typo3/backend/element/spinner-element.js";import"@typo3/backend/element/icon-element.js";let ContentBlocksChooseNameElement=class extends LitElement{constructor(){super(...arguments),this.typo3LangTitle=TYPO3.lang["contentblocks.contentblock.title"]??void 0,this.typo3LangTitleDescription=TYPO3.lang["contentblocks.contentblock.title.description"]??void 0,this.typo3LangDescription=TYPO3.lang["contentblocks.contentblock.description"]??void 0,this.typo3LangVendor=TYPO3.lang["contentblocks.contentblock.vendor"]??void 0,this.typo3LangVendorDescription=TYPO3.lang["contentblocks.contentblock.vendor.description"]??void 0,this.typo3LangPackagename=TYPO3.lang["contentblocks.contentblock.packagename"]??void 0,this.typo3LangPackagenameDescription=TYPO3.lang["contentblocks.contentblock.packagename.description"]??void 0,this._titleInputRef=createRef(),this._vendorInputRef=createRef(),this._packageNameInputRef=createRef()}createRenderRoot(){return this}render(){return html`
      <form class="needs-validation" novalidate>
        <label for="t3-contentblock-title" class="form-label">
          ${this.typo3LangTitle}
        </label>
        <input class="form-control" id="t3-contentblock-title"
          aria-describedby="t3-contentblock-help-title"
          required
          @keydown=${this._onTitleChange}
          @change=${this._onTitleChange}
          @change="${this.updateProperties}"
          ${ref(this._titleInputRef)}
        >
        <div id="t3-contentblocks-help-title" class="form-text">
          ${this.typo3LangTitleDescription}
        </div>

        <div class="row">
          <div class="col">
            <label for="t3-contentblocks-vendor" class="form-label">
              ${this.typo3LangVendor}
            </label>
            <input class="form-control" id="t3-contentblocks-vendor"
              aria-describedby="t3-contentblocks-help-vendor"
              required
              @change="${this.updateProperties}"
              ${ref(this._vendorInputRef)}
            >
            <div id="t3-contentblocks-help-vendor" class="form-text">
              ${this.typo3LangVendorDescription}
            </div>
          </div>
          <div class="col">
            <label for="t3-contentblocks-packagename" class="form-label">
              ${this.typo3LangPackagename}
            </label>
            <input class="form-control" id="t3-contentblocks-packagename"
              aria-describedby="t3-contentblocks-help-packagename"
              required
              @change="${this.updateProperties}"
              ${ref(this._packageNameInputRef)}
            >
            <div id="t3-contentblocks-help-packagename" class="form-text">
              ${this.typo3LangPackagenameDescription}
            </div>
          </div>
        </div>
      </form>
    `}_onTitleChange(e){const t=e.target.value.toLowerCase().replace(/\W+/g,"-");this._packageNameInputRef.value&&(this._packageNameInputRef.value.value=t)}updateProperties(e){this.title=this._titleInputRef.value?.value,this.vendor=this._vendorInputRef.value?.value,this.packageName=this._packageNameInputRef.value?.value}};__decorate([property({attribute:"typo3-lang-title"})],ContentBlocksChooseNameElement.prototype,"typo3LangTitle",void 0),__decorate([property({attribute:"typo3-lang-title-description"})],ContentBlocksChooseNameElement.prototype,"typo3LangTitleDescription",void 0),__decorate([property({attribute:"typo3-lang-description"})],ContentBlocksChooseNameElement.prototype,"typo3LangDescription",void 0),__decorate([property({attribute:"typo3-lang-vendor"})],ContentBlocksChooseNameElement.prototype,"typo3LangVendor",void 0),__decorate([property({attribute:"typo3-lang-vendor-description"})],ContentBlocksChooseNameElement.prototype,"typo3LangVendorDescription",void 0),__decorate([property({attribute:"typo3-lang-packagename"})],ContentBlocksChooseNameElement.prototype,"typo3LangPackagename",void 0),__decorate([property({attribute:"typo3-lang-packagename-description"})],ContentBlocksChooseNameElement.prototype,"typo3LangPackagenameDescription",void 0),ContentBlocksChooseNameElement=__decorate([customElement("typo3-content_blocks-choose-name")],ContentBlocksChooseNameElement);export{ContentBlocksChooseNameElement};