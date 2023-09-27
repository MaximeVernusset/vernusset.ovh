// ==UserScript==
// @name         dl-protect - Uptobox links grabber
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Grab uptobox links from dl-protect.link and redirect to vernusset.ovh collector page
// @author       Maxime Vernusset
// @match        https://dl-protect.link/*
// @grant        none
// ==/UserScript==

const uptoboxLinksGeneratorUrl = 'http://vernusset.ovh/index.php?action=pyload/collect';

function clickSubmitButton() {
    const button = document.getElementById('subButton');
    if (button) {
        button.click();
    }
}

function accessLinks() {
    const observer = new MutationObserver(function() {
        //this.disconnect();
        clickSubmitButton();
    });
    observer.observe(document.getElementsByTagName('body')[0], { attributes: false, childList: true });
}

function collectLinks() {
    const links = [];
    for (const div of document.getElementsByClassName('urls')) {
        for (const ul of div.children) {
            for (const li of ul.getElementsByTagName('li')) {
                for (const a of li.getElementsByTagName('a')) {
                    const link = a.href;
                    links.push(encodeURI(link.replace('&', '%3F')));
                }
            }
        }
    }
    return links;
}

function collectTitle() {
    return document.getElementsByTagName('h3')[0].textContent;
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
