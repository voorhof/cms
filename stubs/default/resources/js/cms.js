/**
 * Load Axios
 */
import axios from 'axios';
window.axios = axios;
indow.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Load Bootstrap 5 frontend toolkit
 */
// Import Bootstrap Icons font
import 'bootstrap-icons/font/bootstrap-icons.scss';

// Import CMS Bootstrap styles
import '../scss/cms.scss';

// Import Popper JS
import * as Popper from '@popperjs/core';
window.Popper = Popper;

// Import all of Bootstrap’s JS
import * as bootstrap  from 'bootstrap';
window.bootstrap  = bootstrap;

/**
 * Extra admin scripts
 */
window.onload = function() {
    showSidebar();
}

/* Show Sidebar Toggler */
function showSidebar() {
    const toggle = document.querySelector('[data-js-class="js-show-sidebar"]');

    if (toggle) {
        toggle.onclick = function() {
            document.body.classList.toggle('js-show-sidebar');
        };
    }
}
