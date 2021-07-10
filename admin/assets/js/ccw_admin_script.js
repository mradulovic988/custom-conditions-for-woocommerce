'use strict';
const saveChangesBtn = document.querySelector('#ccw-save-changes-btn');
const loader = document.querySelector('.ccw-loader');
if (saveChangesBtn) {
    saveChangesBtn.addEventListener('click', () => loader.style.display = 'block');
}