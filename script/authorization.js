//поиск нужных элементов
var email= document.getElementById('email');
var pass= document.getElementById('pass');
var form = document.getElementById('formAuth');
// var modal = document.getElementById('modalWindow');

//тригеры правильности ввода
var emailTrig=false; 
var passTrig=false;

//регулярное выражение для email
const regExEmail= /^[A-Z0-9._%+-]+@[A-Z0-9-]+.+.[A-Z]{2,4}$/i;

//действие на изменение логина/почты
email.onchange=function(){

    if (regExEmail.test(this.value)){
        document.getElementsByClassName('info_email')[0].innerHTML = "";
        emailTrig=true;
    }
    else{
        document.getElementsByClassName('info_email')[0].innerHTML = "у вас ошибка";
        emailTrig=false;
    }        
}

//регулярное выражение для пароля
const regExPass=/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/g;

//действие при изменении пароля
pass.onchange=function(){

    if (regExPass.test(this.value)){
        document.getElementsByClassName('info_pass')[0].innerHTML = "";
        passTrig=true;
    }
    else{
        document.getElementsByClassName('info_pass')[0].innerHTML = "у вас ошибка";
        passTrig=false;
    }

}

//авторизация
form.onsubmit=function(event){

    event.preventDefault();
    
    if(passTrig && emailTrig){
        //запрос на серрвер
        $.ajax({
            type: "post",
            url: "actions\\authorisation.php",
            data: {
                'email':email.value,
                'pass':pass.value,
            },
            dataType: "html",
            success: function (response) {
                if(response==1){
                    window.location='pages\\home.php';
                }else{
                    let modal = new Modal(response);
                    modal.show();
                }
            }
        });
    }
}
