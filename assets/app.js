import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
// import 'bootstrap/dist/css/bootstrap.min.css';
import 'mdb-ui-kit/css/mdb.min.css';
import { Dropdown, Input, Ripple  , initMDB } from 'mdb-ui-kit';
initMDB({ Dropdown, Input, Ripple });

//import $ from 'jquery';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


// Mutation Observer
// Select the node that will be observed for mutations
const targetNode = document.getElementById("search_champions");
// Options for the observer (which mutations to observe)
const config = { attributes: false, childList: true, subtree: true };
// Callback function to execute when mutations are observed
const callback = (mutationList, observer) => {
    [... document.querySelectorAll('[data-mdb-input-init]:not([data-mdb-input-initialized])')].forEach(element => {
        new Input(element).init();
    })
};
// Create an observer instance linked to the callback function
const observer = new MutationObserver(callback);
// Start observing the target node for configured mutations
observer.observe(targetNode, config);
// Later, you can stop observing
// observer.disconnect();


// Form movements :
const form = document.getElementsByName('search')[0];
const formLargePosition = document.getElementById('form-position-large');
const formSmallPosition = document.getElementById('form-position-small');
function moveForm() {
    const windowWidth = window.innerWidth;
    if(windowWidth >= 1200) {
        formLargePosition.appendChild(form);
    } else {
        formSmallPosition.appendChild(form);
    }
}
// Move form on resize :
window.addEventListener('resize', moveForm);
// Move the form when the page is init :
moveForm();

// Event when add button is clicked (fix dropdown closing)
const addButton = document.getElementById('add-button');
addButton.addEventListener('click', function (event){
    event.stopPropagation();
});