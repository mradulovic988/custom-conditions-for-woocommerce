'use strict';
const saveChangesBtn = document.querySelector('#wcc-save-changes-btn');
const loader = document.querySelector('.wcc-loader');
if (saveChangesBtn) {
    saveChangesBtn.addEventListener('click', () => loader.style.display = 'block');
}