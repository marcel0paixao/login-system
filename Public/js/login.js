import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js'

var vue = new Vue({
    el: "#login",
    data: {
        email: '',
        pass: '',
        confirmPass: '',
        errorPass: false,
        name: '',
        birthdate: '',
        remember: false,
        forget: false,
        register: false,
        login: true,
        sentCode: false,
        nullInput: true,
        terms: false
    },
    methods: {
        loginForm(){
            this.register = false
            this.forget = false
            this.login = true
        },
        registerForm(){
            this.register = true
            this.forget = false
            this.login = false
        },
        forgetForm(){
          this.register = false
          this.forget = true
          this.login = false
        },
        sendEmail(){
          this.sentCode = true
        },
        verifyCode(){
          alert('verified')
        },
        confirmPassword(){
          (this.confirmPass === this.pass)? this.errorPass = false : this.errorPass = true
        },
        nullInputs(){
          if (this.email === '' || this.name === '' || this.pass === '' || this.confirmPass === '' || this.birthdate === undefined || this.terms === false || this.birthdate === '') {
            this.disableButton(true)
          } else {
            this.disableButton(false)
          }
        },
        disableButton(bool){
          (bool) ? document.getElementById('signup').setAttribute('disabled', 'disabled') : document.getElementById('signup').removeAttribute('disabled')
        }
    },
    watch: {
      confirmPass(){
        this.nullInputs()
        if (!this.confirmPassword()) {
          this.disableButton(true)
        }
      },
      name(){
        this.nullInputs()
      },
      pass(){
        this.nullInputs()
      },
      email(){
        this.nullInputs()
      },
      birthdate(){
        this.nullInputs()
      },
      terms(){
        this.nullInputs()
        console.log(this.terms);
      }
  }
})