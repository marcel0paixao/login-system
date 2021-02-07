import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js'

new Vue({
    el: "#home",
    data: {
        name: '',
        email: '',
        pass: '',
        birth: '',
        editNameState: false,
        editEmailState: false,
        editBirthState: false,
        checkboxDelete: false,
        nullInput: false
    },
    methods: {
        nameEdited(){
            this.editNameState = !this.editNameState
        },
        emailEdited(){
            this.editEmailState = !this.editEmailState
        },
        birthEdited(){
            this.editBirthState = !this.editBirthState
        },
        back(){
            window.location.href="/home"
        },
        nullInputs(){
            if (this.pass === '' || this.pass === undefined || this.checkboxDelete === false) {
              this.disableButton(true)
            } else {
              this.disableButton(false)
            }
        },
        disableButton(bool){
            (bool) ? document.getElementById('delete').setAttribute('disabled', 'disabled') : document.getElementById('delete').removeAttribute('disabled')
        }
    },
    watch: {
        checkboxDelete(){
            console.log(this.checkboxDelete);
            this.nullInputs()
        },
        pass(){
            this.nullInputs()
            if(this.pass === '') {
                document.getElementById('pass').className = 'wrongPassConfirm'
                document.getElementById('passIcon').style = 'color: red;'
            }
        }
    }
})