// ==UserScript==
// @name         ed-protect - links grabber
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Grab uptobox/1fichier links from ed-protect.org and redirect to vernusset.ovh collector page
// @author       Maxime Vernusset
// @match        https://ed-protect.org/*
// @grant        none
// ==/UserScript==

const uptoboxLinksGeneratorUrl = 'http://vernusset.ovh/index.php?action=pyload/collect';

function closeAntiAdblockPopup() {
    const spanHtmlCollection = document.getElementsByTagName('span');
    const crossIndex = Array.from(spanHtmlCollection).findIndex(span => span.innerHTML === '✖');
    spanHtmlCollection[crossIndex].click();
}

function clickSubmitButton() {
    const button = document.getElementById('submit_button');
    if (button) {
        button.click();
    }
}

function accessLinks() {
    const observer = new MutationObserver(function() {
        this.disconnect();
        //closeAntiAdblockPopup();
        clickSubmitButton();
    });
    observer.observe(document.getElementsByTagName('body')[0], { attributes: false, childList: true });
}

function collectLinks() {
    const links = [];
    for (let span of document.getElementsByClassName('lien')) {
        links.push(encodeURI(span.firstChild.href));
    }
    return links;
}

function collectTitle() {
    return document.getElementsByTagName('h2')[0].textContent;
}

(function() {
    'use strict';
    accessLinks();
    const links = collectLinks();
    const title = collectTitle();
    if (links.length > 0) {
        document.location=encodeURI(`${uptoboxLinksGeneratorUrl}&links=${JSON.stringify(links)}&name=${title}`);
    }
})();
