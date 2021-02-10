import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js'

var vue = new Vue({
    el: "#email",
    data: {
        pass: '',
        confirmPass: '',
        errorPass: false,
    },
    methods: {
        confirmPassword(){
          return (this.confirmPass === this.pass)? true : false
        },
        nullInputs(){
          if (this.pass === '' || this.confirmPass === '' || !this.confirmPassword()) {
            this.disableButton(true)
          } else {
            this.disableButton(false)
          }
        },
        disableButton(bool){
          (bool) ? document.getElementById('continue').setAttribute('disabled', 'disabled') : document.getElementById('continue').removeAttribute('disabled')
        }
    },
    watch: {
      confirmPass(){
        if (!this.confirmPassword()) {
          this.errorPass = true
        } else {
          this.errorPass = false
        }
        this.nullInputs()
      },
      pass(){
        this.nullInputs()
      }
   }
})
