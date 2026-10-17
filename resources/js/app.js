require("./bootstrap");
require("./plugins/vue-toastification");
import Vue from "vue";
import {
  App as InertiaApp,
  plugin as InertiaPlugin
} from "@inertiajs/inertia-vue";
import 'sweetalert2/dist/sweetalert2.min.css'
import VueSweetalert2 from 'vue-sweetalert2'

import { Link } from "@inertiajs/inertia-vue";
import vuetify from "./plugins/vuetify";
import { InertiaProgress } from "@inertiajs/progress";

Vue.use(InertiaPlugin);
Vue.component("Link", Link);
Vue.mixin({ methods: { route: window.route } });
const app = document.getElementById("app");

const options = {
  cancelButtonColor: '#F44336',
  confirmButtonColor: 'primary',
  confirmButtonText: 'Ok',
  cancelButtonText: 'Annuler',
  reverseButtons: true
};


Vue.prototype.$alert = {
  confirm: (title, text = '', callBack) => {
      Vue.prototype.$swal({
          title,
          text,
          showCancelButton: true,
          icon: 'warning',
      }).then(res => {
          if(res.isConfirmed) {
              callBack()
          }
      })
  },
  success: (text = '') => {
      Vue.prototype.$swal({
          title: 'SUCCES',
          text,
          icon: 'success',
          confirmButtonColor: '#33ba6d',

      })
  },

  error: (text = '') => {
      Vue.prototype.$swal('ERREUR', text, 'error')
  },

  info: (text = '') => {
      Vue.prototype.$swal('INFO', text, 'info')
  },
  warning: (text = '') => {
      Vue.prototype.$swal('AVERTISSEMENT', text, 'warning')
  },

  messages: (messages= {}) => {
      const keys = Object.keys(messages)
      let text = ''
      keys.forEach((v) => {
          text+= `<h5 style="color: red">${messages[v]}</h5>`
      })
      Vue.prototype.$swal('ERREUR', text, 'error')

  }
}
Vue.use(VueSweetalert2, options);

new Vue({
  vuetify,
  render: h =>
    h(InertiaApp, {
      props: {
        title: title => `${title} - My App`,
        initialPage: JSON.parse(app.dataset.page),
        resolveComponent: name => require(`./pages/${name}`).default
      }
    })
}).$mount(app);

InertiaProgress.init({ color: "#fff" });
