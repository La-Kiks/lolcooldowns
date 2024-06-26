// assets/controllers/form-collection_controller.js

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["collectionContainer"]

    static values = {
        index    : Number,
        prototype: String,
    }

    addCollectionElement(event)
    {
        if(this.indexValue < 10){
            const item = document.createElement('li');
            item.innerHTML = this.prototypeValue.replace(/__name__/g, this.indexValue);
            this.collectionContainerTarget.appendChild(item.firstElementChild);
            this.indexValue++;

            if(this.indexValue  === 10){
                const button = document.getElementById('add-button');
                button.setAttribute('disabled', '');
            }
        }
    }

}