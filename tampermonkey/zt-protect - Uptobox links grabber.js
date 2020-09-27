// ==UserScript==
// @name         zt-protect - Uptobox links grabber
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Grab uptobox links from zt-protect.com and redirect to downloadLinks generator service
// @author       You
// @match        https://zt-protect.com/*
// @grant        none
// ==/UserScript==

const uptoboxLinksGeneratorUrl = 'http://vernusset.ovh/index.php?action=pyload/collect';

function clickSubmitButton() {
    const button = document.getElementsByClassName('btn btn-primary')	[0];
    if (button) {
        button.click();
    }
}

function accessLinks() {
    const observer = new MutationObserver(function() {
        this.disconnect();
        clickSubmitButton();
    });
    observer.observe(document.getElementsByTagName('body')[0], { attributes: false, childList: true });
}

function collectLinks() {
    const links = [];
    for (let span of document.getElementsByClassName('showURL')) {
        links.push(encodeURI(span.parentElement.href));
    }
    return links;
}

(function() {
    'use strict';
    accessLinks();
    const links = collectLinks();
    if (links.length > 0) {
        document.location=encodeURI(`${uptoboxLinksGeneratorUrl}&links=${JSON.stringify(links)}`);
    }
})();
