import {createApp} from 'vue'

import HelloWorld from './components/HelloWorld.vue'

const components = {
    HelloWorld
}

for (const el of document.getElementsByClassName('vue-app')) {
    createApp({
        template: el.innerHTML,
        components
    }).mount(el)
}
